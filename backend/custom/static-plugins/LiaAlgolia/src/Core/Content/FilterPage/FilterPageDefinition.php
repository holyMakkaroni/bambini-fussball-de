<?php
namespace Lia\Algolia\Core\Content\FilterPage;

use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Lia\Algolia\Core\Content\FilterPage\Translation\FilterPageTranslationDefinition;

class FilterPageDefinition extends EntityDefinition
{
    public const string ENTITY_NAME = 'lia_algolia_filter_page';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return FilterPageCollection::class;
    }

    public function getEntityClass(): string
    {
        return FilterPageEntity::class;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('category_id', 'categoryId', \Shopware\Core\Content\Category\CategoryDefinition::class),
            new StringField('meta_robots', 'metaRobots'),

            new ManyToManyAssociationField(
                'salesChannels',
                \Shopware\Core\System\SalesChannel\SalesChannelDefinition::class,
                FilterPageSalesChannelDefinition::class,
                'filter_page_id',
                'sales_channel_id'
            ),

            new ManyToManyAssociationField(
                'properties',
                PropertyGroupOptionDefinition::class,
                FilterPagePropertyDefinition::class,
                'filter_page_id',
                'property_group_option_id'
            ),

            new TranslationsAssociationField(FilterPageTranslationDefinition::class, 'lia_algolia_filter_page_id'),
        ]);
    }
}
