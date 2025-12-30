<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Indexing;

use Algolia\AlgoliaSearch\SearchIndex;
use Lia\Algolia\Factory\ClientFactory;
use Lia\Algolia\Framework\DataAbstractionLayer\CriteriaParser;
use Lia\Algolia\Service\AlgoliaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class IndexCreator
{
    const INDEX_REPLICAS = [
        [
            'name' => 'sales',
            'sort' => ['asc']
        ],
        [
            'name' => 'ratingAverage',
            'sort' => ['desc', 'asc']
        ],
        [
            'name' => 'inSale',
            'sort' => ['desc']
        ]
    ];

    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly AlgoliaService $algoliaService,
        private readonly EntityRepository $propertyGroupRepository,
    )
    {
    }

    private function getFilterablePropertyGroups(): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('filterable', true));

        return $this->propertyGroupRepository->search($criteria, Context::createDefaultContext())->getElements();
    }

    public function createIndex(EntityDefinition $definition, string $salesChannelId, string $languageId, ?string $localeCode): SearchIndex
    {
        if($this->indexExists($definition, $salesChannelId, $languageId)->exists()) {
            $this->clientFactory->setSalesChannelId($salesChannelId);

            $this->deleteIndex($definition, $salesChannelId, $languageId);

            foreach(self::INDEX_REPLICAS as $replica) {
                $this->clientFactory->createClient()->initIndex(`{$salesChannelId}_{$languageId}_{$replica}`)->delete();
            }
        }

        $propertyGroups = $this->getFilterablePropertyGroups();

        $filterablePropertyGroups = [];
        foreach ($propertyGroups as $propertyGroup) {
            $filterablePropertyGroups[] = $propertyGroup->getName();
        }


        $this->clientFactory->setSalesChannelId($salesChannelId);
        $indexLanguages = $localeCode ? [$localeCode] : [];
        $index = $this->clientFactory->createClient()->initIndex($this->algoliaService->getIndexName($definition, $salesChannelId, $languageId));
        $replicas = $this->generateReplicaIndexes($salesChannelId, $languageId);

        if($definition->getEntityName() === 'product') {
            $index->setSettings([
                'indexLanguages' => $indexLanguages,
                'queryLanguages' => $indexLanguages,
                'replicas' => $replicas,
                'ranking' => [
                    'desc(sales)',
                    'typo',
                    'geo',
                    'words',
                    'filters',
                    'proximity',
                    'attribute',
                    'exact',
                    'custom'
                ],
                'searchableAttributes' => [
                    'productNumber',
                    'name'
                ],
                'attributesForFaceting' => [
                    'id',
                    'parentId',
                    'active',
                    'categoriesRo',
                    'visibilities.salesChannelId',
                    'afterDistinct(displayGroup)',
                    'propertyIds',
                    'optionIds',
                    'manufacturerId',
                    'manufacturer',
                    'searchable(customSearchKeywords)',
                    'searchable(categories)',
                    'hierarchicalCategories.lvl0',
                    'hierarchicalCategories.lvl1',
                    'hierarchicalCategories.lvl2',
                    'hierarchicalCategories.lvl3',
                    'hierarchicalCategories.lvl4',
                    'hierarchicalCategories.lvl5',
                    'defaultPricing.inSale',
                    'defaultPricing.gross',
                    ...$filterablePropertyGroups
                ],
                'numericAttributesForFiltering' => CriteriaParser::NUMERIC_ATTRIBUTES,
                'attributeForDistinct' => 'displayGroup',
                'distinct' => true,
            ]);

            foreach(self::INDEX_REPLICAS as $replica) {
                foreach ($replica['sort'] as $sortOrder) {
                    $replicaIndexName = $salesChannelId . '_' . $languageId . '_product_' . $replica['name'] . '_' . $sortOrder;
                    $this->clientFactory->createClient()->initIndex($replicaIndexName)->setSettings([
                        'indexLanguages' => $indexLanguages,
                        'queryLanguages' => $indexLanguages,
                        'ranking' => [
                            $sortOrder . '('. $replica['name'] .')',
                            'typo',
                            'geo',
                            'words',
                            'filters',
                            'proximity',
                            'attribute',
                            'exact',
                            'custom'
                        ],
                        'searchableAttributes' => [
                            'productNumber',
                            'name'
                        ],
                        'attributesForFaceting' => [
                            'id',
                            'parentId',
                            'active',
                            'categoriesRo',
                            'visibilities.salesChannelId',
                            'afterDistinct(displayGroup)',
                            'propertyIds',
                            'optionIds',
                            'manufacturerId',
                            'manufacturer',
                            'searchable(customSearchKeywords)',
                            'searchable(categories)',
                            'hierarchicalCategories.lvl0',
                            'hierarchicalCategories.lvl1',
                            'hierarchicalCategories.lvl2',
                            'hierarchicalCategories.lvl3',
                            'defaultPricing.inSale',
                            'defaultPricing.gross',
                            ...$filterablePropertyGroups
                        ],
                        'attributeForDistinct' => 'displayGroup',
                        'distinct' => true,
                    ]);
                }
            }
        }

        return $index;
    }

    public function indexExists(EntityDefinition $definition, string $salesChannelId, string $languageId): SearchIndex
    {
        $this->clientFactory->setSalesChannelId($salesChannelId);

        return $this->clientFactory->createClient()->initIndex($this->algoliaService->getIndexName($definition, $salesChannelId, $languageId));
    }

    public function deleteIndex(EntityDefinition $definition, string $salesChannelId, string $languageId): void
    {
        $this->clientFactory->setSalesChannelId($salesChannelId);
        $this->clientFactory->createClient()->initIndex($this->algoliaService->getIndexName($definition, $salesChannelId, $languageId))->delete();
    }

    private function generateReplicaIndexes(string $salesChannelId, string $languageId): array
    {
        $replicas = [];

        foreach (self::INDEX_REPLICAS as $replica) {
            foreach ($replica['sort'] as $sortOrder) {
                $replicas[] = "{$salesChannelId}_{$languageId}_product_{$replica['name']}_{$sortOrder}";
            }
        }

        return $replicas;
    }
}