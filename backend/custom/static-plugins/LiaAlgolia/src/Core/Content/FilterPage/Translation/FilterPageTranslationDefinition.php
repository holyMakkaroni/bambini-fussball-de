<?php
namespace Lia\Algolia\Core\Content\FilterPage\Translation;

use Lia\Algolia\Core\Content\FilterPage\FilterPageDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;

class FilterPageTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'lia_algolia_filter_page_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return FilterPageTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return FilterPageTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return FilterPageDefinition::class;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new StringField('name', 'name'),
            new LongTextField('seo_text', 'seoText'),
            new StringField('meta_title', 'metaTitle'),
            new StringField('meta_description', 'metaDescription'),
        ]);
    }
}
