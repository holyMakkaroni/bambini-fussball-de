<?php declare(strict_types=1);

namespace Lia\Algolia\Product;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;

class ProductSearchQueryBuilder extends AbstractProductSearchQueryBuilder
{
    public function __construct(
        private readonly EntityDefinitionQueryHelper $entityDefinitionQueryHelper,
        private readonly EntityDefinition $productDefinition
    )
    {
    }

    public function getDecorated(): AbstractProductSearchQueryBuilder
    {
        throw new DecorationPatternException(self::class);
    }

    public function build(Criteria $criteria, Context $context)
    {
        return $context;
    }
}