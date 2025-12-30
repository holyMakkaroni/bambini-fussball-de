<?php declare(strict_types=1);

namespace Lia\Algolia\Product;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

abstract class AbstractProductSearchQueryBuilder
{
    abstract public function getDecorated(): AbstractProductSearchQueryBuilder;

    abstract public function build(Criteria $criteria, Context $context);
}