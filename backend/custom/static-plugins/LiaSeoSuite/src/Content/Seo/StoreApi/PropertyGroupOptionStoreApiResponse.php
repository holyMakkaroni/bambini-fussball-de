<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo\StoreApi;

use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class PropertyGroupOptionStoreApiResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult<PropertyGroupOptionCollection>
     */
    protected $object;

    /**
     * @param EntitySearchResult<PropertyGroupOptionCollection> $object
     */
    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getPropertyGroupCollection(): PropertyGroupOptionCollection
    {
        return $this->object->getEntities();
    }
}