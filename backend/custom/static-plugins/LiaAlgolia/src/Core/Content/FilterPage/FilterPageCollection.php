<?php
namespace Lia\Algolia\Core\Content\FilterPage;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(FilterPageEntity $entity)
 * @method void                    set(string $key, FilterPageEntity $entity)
 * @method FilterPageEntity[]     getIterator()
 * @method FilterPageEntity[]     getElements()
 * @method FilterPageEntity|null  get(string $key)
 * @method FilterPageEntity|null  first()
 * @method FilterPageEntity|null  last()
 */
class FilterPageCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FilterPageEntity::class;
    }
}
