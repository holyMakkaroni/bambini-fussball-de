<?php
declare(strict_types=1);

namespace Lia\Checkout\Storefront\Subscriber;

use Lia\Checkout\Core\Checkout\Cart\DeliveryCalculator;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class CheckoutConfirmSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SystemConfigService $systemConfigService,
        private EntityRepository $shippingMethodRepository,
        private DeliveryCalculator $deliveryCalculator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutConfirmPageLoadedEvent::class => [
                ['calculateShippingCost', 0],
            ],
        ];
    }

    public function calculateShippingCost(CheckoutConfirmPageLoadedEvent $event): void
    {
        $context = $event->getContext();
        $shippingMethodIds = $event->getPage()->getShippingMethods()->getIds();

        $shippingMethods = new ShippingMethodCollection($this->shippingMethodRepository->search((new Criteria($shippingMethodIds))->addAssociations(['prices', 'tax']), $context));

        foreach ($shippingMethods as $id => $shippingMethod) {
            $cost = $this->deliveryCalculator->calculateShippingMethod($shippingMethod, $event->getPage()->getCart(), $event->getSalesChannelContext());

            if (!$cost) {
                continue;
            }

            $event->getPage()->getShippingMethods()->get($id)->addExtension('calculatedShippingCosts', $cost);
        }
    }
}
