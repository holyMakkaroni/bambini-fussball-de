<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo\StoreApi;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
class PropertyGroupOptionRoute extends AbstractPropertyGroupOptionRoute
{
    private EntityRepository $propertyRepository;

    public function __construct(EntityRepository $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function getDecorated(): AbstractPropertyGroupOptionRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/search/property-group-option', name: 'store-api.search.property-group-option', defaults: ['_entity' => 'property_group_option'], methods: ['POST'])]
    public function load(Criteria $criteria, SalesChannelContext $context): PropertyGroupOptionStoreApiResponse
    {
        return new PropertyGroupOptionStoreApiResponse($this->propertyRepository->search($criteria, $context->getContext()));
    }
}