<?php
namespace Lia\Algolia\Core\Content\FilterPage\Translation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                             add(FilterPageTranslationEntity $entity)
 * @method void                             set(string $key, FilterPageTranslationEntity $entity)
 * @method FilterPageTranslationEntity[]    getIterator()
 * @method FilterPageTranslationEntity[]    getElements()
 * @method FilterPageTranslationEntity|null get(string $key)
 * @method FilterPageTranslationEntity|null first()
 * @method FilterPageTranslationEntity|null last()
 */
class FilterPageTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FilterPageTranslationEntity::class;
    }
}
