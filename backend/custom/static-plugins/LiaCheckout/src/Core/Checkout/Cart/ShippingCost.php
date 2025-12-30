<?php
declare(strict_types=1);

namespace Lia\Checkout\Core\Checkout\Cart;

use Shopware\Core\Framework\Struct\Struct;

class ShippingCost extends Struct
{
    public function __construct(
        public readonly float $price
    ) {
    }
}
