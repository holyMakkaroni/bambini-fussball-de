<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo\StoreApi;

use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

class PropertyGroupStoreApiResponse extends StoreApiResponse
{
    /**
     * @var EntitySearchResult<PropertyGroupCollection>
     */
    protected $object;

    /**
     * @param EntitySearchResult<PropertyGroupCollection> $object
     */
    public function __construct(EntitySearchResult $object)
    {
        parent::__construct($object);
    }

    public function getPropertyGroupCollection(): PropertyGroupCollection
    {
        return $this->object->getEntities();
    }
}