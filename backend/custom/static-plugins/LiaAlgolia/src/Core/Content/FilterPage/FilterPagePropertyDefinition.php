<?php
namespace Lia\Algolia\Core\Content\FilterPage;

use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;

class FilterPagePropertyDefinition extends MappingEntityDefinition
{
    public const string ENTITY_NAME = 'lia_algolia_filter_page_property';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new FkField('filter_page_id', 'filterPageId', FilterPageDefinition::class),
            new FkField('property_group_option_id', 'propertyGroupOptionId', PropertyGroupOptionDefinition::class),
        ]);
    }
}
