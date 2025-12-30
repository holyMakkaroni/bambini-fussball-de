<?php declare(strict_types=1);

namespace Lia\Algolia\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class SalesChannelService
{
    public function __construct(
        private readonly EntityRepository $salesChannelRepository,
    ){}

    public function getAllSalesChannels(array $salesChannelIds = []): EntitySearchResult
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        $criteria->addAssociation('languages');

        if (count($salesChannelIds)) {
            $salesChannelFilters = [];

            foreach ($salesChannelIds as $salesChannelId) {
                $salesChannelFilters[] = new EqualsFilter('id', $salesChannelId);
            }

            $criteria->addFilter(
                new MultiFilter(
                    MultiFilter::CONNECTION_OR,
                    $salesChannelFilters
                )
            );
        }

        return $this->salesChannelRepository->search($criteria, $context);
    }
}