<?php
declare(strict_types=1);

namespace Lia\Checkout\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\PercentageTaxRuleBuilder;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodPrice\ShippingMethodPriceCollection;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodPrice\ShippingMethodPriceEntity;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\PriceCollection;
use Shopware\Core\Framework\Util\FloatComparator;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

readonly class DeliveryCalculator
{
    public const CALCULATION_BY_LINE_ITEM_COUNT = 1;

    public const CALCULATION_BY_PRICE = 2;

    public const CALCULATION_BY_WEIGHT = 3;

    public const CALCULATION_BY_VOLUME = 4;

    public function __construct(
        private QuantityPriceCalculator $priceCalculator,
        private PercentageTaxRuleBuilder $percentageTaxRuleBuilder
    ) {
    }

    public function calculateShippingMethod(ShippingMethodEntity $shippingMethod, Cart $cart, SalesChannelContext $context): ?ShippingCost {
        try {
            $costs = null;
            foreach ($context->getRuleIds() as $ruleId) {
                $shippingPrices = $shippingMethod->getPrices()->filterByProperty('ruleId', $ruleId);

                $costs = $this->getMatchingPriceOfRule($shippingMethod, $cart, $context, $shippingPrices);
                if ($costs !== null) {
                    break;
                }
            }

            if ($costs === null) {
                $shippingPrices = $shippingMethod->getPrices()->filterByProperty('ruleId', null);
                $costs = $this->getMatchingPriceOfRule($shippingMethod, $cart, $context, $shippingPrices);
            }

            return new ShippingCost($costs->getTotalPrice() ?? 0.0);
        } catch (\Exception $exception) {}

        return null;
    }

    private function matches(Cart $cart, ShippingMethodPriceEntity $shippingMethodPrice, SalesChannelContext $context): bool {
        if ($shippingMethodPrice->getCalculationRuleId()) {
            return \in_array($shippingMethodPrice->getCalculationRuleId(), $context->getRuleIds(), true);
        }

        $start = $shippingMethodPrice->getQuantityStart();
        $end = $shippingMethodPrice->getQuantityEnd();

        $value = match ($shippingMethodPrice->getCalculation()) {
            self::CALCULATION_BY_PRICE => $cart->getPrice()->getTotalPrice(),
            self::CALCULATION_BY_LINE_ITEM_COUNT => $cart->getLineItems()->getTotalQuantity(),
            self::CALCULATION_BY_WEIGHT => array_sum($cart->getLineItems()->fmap(function (LineItem $lineItem) {
                return $lineItem->getDeliveryInformation()?->getWeight() ?? 0.00;
            })),
            self::CALCULATION_BY_VOLUME => array_sum($cart->getLineItems()->fmap(function (LineItem $lineItem) {
                return $lineItem->getDeliveryInformation()?->getVolume() ?? 0.00;
            })),
            default => $cart->getPrice()->getTotalPrice() / 100,
        };

        return (!$start || FloatComparator::greaterThanOrEquals($value, $start)) && (!$end || FloatComparator::lessThanOrEquals($value, $end));
    }

    private function calculateShippingCosts(ShippingMethodEntity $shippingMethod, PriceCollection $priceCollection, LineItemCollection $calculatedLineItems, SalesChannelContext $context): CalculatedPrice|false {
        $rules = null;

        switch ($shippingMethod->getTaxType()) {
            case ShippingMethodEntity::TAX_TYPE_HIGHEST:
                $rules = $calculatedLineItems->getPrices()->getHighestTaxRule();
                break;

            case ShippingMethodEntity::TAX_TYPE_FIXED:
                $tax = $shippingMethod->getTax();
                if ($tax !== null) {
                    $rules = $context->buildTaxRules($tax->getId());
                }
                break;

            default:
                $rules = $this->percentageTaxRuleBuilder->buildRules(
                    $calculatedLineItems->getPrices()->sum()
                );
        }

        $price = $this->getCurrencyPrice($priceCollection, $context);

        if(!$rules) {
            return false;
        }

        $definition = new QuantityPriceDefinition($price, $rules, 1);

        return $this->priceCalculator->calculate($definition, $context);
    }

    private function getCurrencyPrice(PriceCollection $priceCollection, SalesChannelContext $context): float
    {
        $price = $priceCollection->getCurrencyPrice($context->getCurrency()->getId());

        $value = $this->getPriceForTaxState($price, $context);

        if ($price->getCurrencyId() === Defaults::CURRENCY) {
            $value *= $context->getContext()->getCurrencyFactor();
        }

        return $value;
    }

    private function getPriceForTaxState(Price $price, SalesChannelContext $context): float
    {
        if ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            return $price->getGross();
        }

        return $price->getNet();
    }

    private function getMatchingPriceOfRule(ShippingMethodEntity $shippingMethod, Cart $cart, SalesChannelContext $context, ShippingMethodPriceCollection $shippingPrices): ?CalculatedPrice {
        $shippingPrices->sort(
            function (ShippingMethodPriceEntity $priceEntityA, ShippingMethodPriceEntity $priceEntityB) use ($context) {
                $priceA = $this->getCurrencyPrice($priceEntityA->getCurrencyPrice(), $context);
                $priceB = $this->getCurrencyPrice($priceEntityB->getCurrencyPrice(), $context);

                return $priceA <=> $priceB;
            }
        );

        $costs = null;
        foreach ($shippingPrices as $shippingPrice) {
            if (!$this->matches($cart, $shippingPrice, $context)) {
                continue;
            }

            $price = $shippingPrice->getCurrencyPrice();
            if (!$price) {
                continue;
            }

            $costs = $this->calculateShippingCosts(
                $shippingMethod,
                $price,
                $cart->getLineItems(),
                $context
            );
            break;
        }

        return $costs;
    }
}
