<?php
namespace Lia\Algolia\Core\Content\FilterPage;

use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\System\SalesChannel\SalesChannelDefinition;

class FilterPageSalesChannelDefinition extends MappingEntityDefinition
{
    public const string ENTITY_NAME = 'lia_algolia_filter_page_sales_channel';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new FkField('filter_page_id', 'filterPageId', FilterPageDefinition::class),
            new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class),
        ]);
    }
}
