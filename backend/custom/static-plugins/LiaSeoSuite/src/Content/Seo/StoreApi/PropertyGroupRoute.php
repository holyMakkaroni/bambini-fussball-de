<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class PropertyGroupRoute extends AbstractPropertyGroupRoute
{
    private EntityRepository $propertyGroupRepository;

    public function __construct(EntityRepository $propertyGroupRepository)
    {
        $this->propertyGroupRepository = $propertyGroupRepository;
    }

    public function getDecorated(): AbstractPropertyGroupRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/search/property-group', name: 'store-api.search.property-group', defaults: ['_entity' => 'property_group'], methods: ['POST'])]
    public function load(Criteria $criteria, SalesChannelContext $context): PropertyGroupStoreApiResponse
    {
        return new PropertyGroupStoreApiResponse($this->propertyGroupRepository->search($criteria, $context->getContext()));
    }
}