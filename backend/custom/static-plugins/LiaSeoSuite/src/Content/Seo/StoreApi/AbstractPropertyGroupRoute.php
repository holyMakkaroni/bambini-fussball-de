<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractPropertyGroupRoute
{
    abstract public function getDecorated(): AbstractPropertyGroupRoute;

    abstract public function load(Criteria $criteria, SalesChannelContext $context): PropertyGroupStoreApiResponse;
}