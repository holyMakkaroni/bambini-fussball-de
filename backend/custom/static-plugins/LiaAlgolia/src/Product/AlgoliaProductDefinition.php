<?php declare(strict_types=1);

namespace Lia\Algolia\Product;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Lia\Algolia\Framework\AbstractAlgoliaDefinition;
use Lia\Algolia\Framework\AlgoliaIndexingUtils;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\SqlHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class AlgoliaProductDefinition extends AbstractAlgoliaDefinition
{
    CONST PARENT_PRODUCT_IDICATOR = '_EP';
    const ASSOCIATION = [
        'seoUrls',
        'cover',
        'prices',
        'deliveryTime',
        'options',
        'properties.group',
        'categories',
        'visibilities',
        'manufacturer'
    ];

    public function __construct(
        private readonly EntityDefinition $definition,
        private readonly AbstractProductSearchQueryBuilder $searchQueryBuilder,
        private readonly EntityRepository $productRepository,
        private readonly EntityRepository $categoryRepository,
        private readonly Connection $connection,
    )
    {
    }

    public function getEntityDefinition(): EntityDefinition
    {
        return $this->definition;
    }

    /**
     * @inheritDoc
     */
    public function getMapping(Context $context): array
    {
        return [];
    }

    public function buildTermQuery(Context $context, Criteria $criteria)
    {
        return $this->searchQueryBuilder->build($criteria, $context);
    }

    /**
     * @param array $ids
     * @param Context $context
     * @return array
     * @throws Exception
     * @throws \JsonException
     * @throws \Exception
     */
    public function fetch(array $ids, Context $context, string $salesChannelId = '', string $navigationCategoryId = ''): array
    {
        $criteria = new Criteria($ids);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('visibilities.salesChannelId', $salesChannelId)
        );

        foreach(self::ASSOCIATION as $association) {
            $criteria->addAssociation($association);
        }

        $data = json_decode(json_encode($this->productRepository->search($criteria, $context)->getElements()), true);

        if (empty($data)) {
            return [];
        }

        $documents = [];

        $categoryCriteria = new Criteria();
        $categoryCriteria->addFilter(
            new EqualsFilter('id', $navigationCategoryId)
        );

        $navigationCategoryData = $this->categoryRepository->search($categoryCriteria, $context)->first();

        $navigationCategoryName = $navigationCategoryData->getName();

        foreach ($data as $id => $item) {
            if($this->skipParentProduct($item)) {
                continue;
            }

            $parentId = $item['parentId'];
            $parentProduct = [];

            if($parentId) {
                $parentProduct = $this->fetchParentProduct($parentId, $context);
            }

            $prices = $this->getPrices($item);
            $seoUrl = $item['seoUrls'][0]['seoPathInfo'] ?? '#';
            $properties = $this->collectProperties($id);

            $categories = [];
            $categoryBreadcrumbs = [];

            $_product = $parentId ? $parentProduct : $item;
            $_categories = $this->filterCategoryByNavigationCategoryName($_product['categories'], $navigationCategoryName);

            foreach ($_categories as $category) {
                $categories[] = AlgoliaIndexingUtils::getTranslation($category, 'name');
                $categoryBreadcrumbs[] = $category['breadcrumb'];
            }
            $hierarchicalCategories = $navigationCategoryName ? $this->buildHierarchicalCategories($categoryBreadcrumbs, $navigationCategoryName) : [];

            $documents[$id] = [
                'id' => $id,
                'autoIncrement' => $item['autoIncrement'],
                'active' => $item['active'],
                'name' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'name', true),
                'productNumber' => $item['productNumber'],
                'ean' => $item['ean'],
                'displayGroup' => $item['displayGroup'],
                'ratingAverage' => $item['ratingAverage'] ?? 0,
                'sales' => (int) $item['sales'],
                'availableStock' => $item['availableStock'],
                'customSearchKeywords' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'customSearchKeywords', true),
                'width' => (float) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'width'),
                'length' => (float) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'length'),
                'height' => (float) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'height'),
                'shippingFree' => (bool) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'shippingFree'),
                'markAsTopseller' => (bool) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'markAsTopseller'),
                'url' => $seoUrl,
                'manufacturer' => AlgoliaIndexingUtils::getProductInheriteValues($item['manufacturer'], $parentProduct, 'name', true),
                ...$prices,
                ...$properties,
                'parentId' => $parentId,
                'available' => (bool) $item['available'],
                'isCloseout' => (bool) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'isCloseout'),
                'stock' => (int) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'stock'),
                'weight' => (float) AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'weight'),
                'manufacturerId' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'manufacturerId'),
                'manufacturerNumber' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'manufacturerNumber'),
                'propertyIds' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'propertyIds'),
                'optionIds' => AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'optionIds'),
                'categoryTree' => $item['categoryTree'] ?? [],
                'categoriesRo' => array_values(array_map(fn (string $categoryId) => ['id' => $categoryId], $item['categoryTree'] ?? [])),
                'categoryIds' => $item['categoryIds'] ?? [],
                'tagIds' => $item['tagIds'] ?? [],
                'states' => $item['states'] ?? [],
                'childCount' => $item['childCount'],
                'visibilities' => array_map(function ($salesChannel) {
                    return [
                        'id' => $salesChannel['id'],
                        'salesChannelId' => $salesChannel['salesChannelId'],
                        'visibility' => $salesChannel['visibility'],
                    ];
                }, AlgoliaIndexingUtils::getProductInheriteValues($item, [], 'visibilities') ?? []),
                'properties' => array_values(array_map(fn (array $property) => ['id' => $property['id'], 'name' => AlgoliaIndexingUtils::stripText(AlgoliaIndexingUtils::getTranslation($property, 'name') ?? '')], $item['properties'] ?? [])),
                'options' => array_values(array_map(fn (array $option) => ['id' => $option['id'], 'name' => AlgoliaIndexingUtils::stripText(AlgoliaIndexingUtils::getTranslation($option, 'name') ?? '')], $item['options'] ?? []))
            ];

            if($item['deliveryTime']) {
                $documents[$id]['deliveryTime'] = [
                    'min' => $item['deliveryTime']['min'],
                    'max' => $item['deliveryTime']['max'],
                    'unit' => $item['deliveryTime']['unit']
                ];
            }

            $image = AlgoliaIndexingUtils::getProductInheriteValues($item, $parentProduct, 'cover');
            if($image) {
                $documents[$id]['image'] = [
                    'path' => $image['media']['path'],
                    'alt' =>  AlgoliaIndexingUtils::getTranslation($image['media'], 'alt'),
                    'title' => AlgoliaIndexingUtils::getTranslation($image['media'], 'title')
                ];
            }

            if(count($categories) > 0) {
                $documents[$id]['categories'] = $categories;
            }

            if(count($hierarchicalCategories) > 0) {
                $documents[$id]['hierarchicalCategories'] = $hierarchicalCategories;
            }
        }

        return $documents;
    }

    private function filterCategoryByNavigationCategoryName($categories, $navigationCategoryName): array
    {
        return array_values(array_filter($categories, function($item) use ($navigationCategoryName) {
            return isset($item['breadcrumb'][0]) && $item['breadcrumb'][0] === $navigationCategoryName;
        }));
    }

    /**
     * @param string $id
     * @param Context $context
     * @return array
     */
    private function fetchParentProduct (string $id, Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $id));

        foreach(self::ASSOCIATION as $association) {
            $criteria->addAssociation($association);
        }

        return json_decode(json_encode($this->productRepository->search($criteria, $context)->first()), true);
    }

    private function collectProperties(string $productId): array
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('properties.media');
        $criteria->addFilter(new EqualsFilter('properties.group.filterable', true));

        /** @var ProductEntity|null $product */
        $product = $this->productRepository->search($criteria, $context)->first();

        if (!$product) {
            return [];
        }

        $result = [];

        $properties = $product->getProperties();
        if ($properties->count() > 0) {
            foreach ($properties->getElements() as $property) {
                $group = $property->getGroup();

                if ($group) {
                    $groupName = $group->getName();
                    $propertyName = $property->getName();

                    if (!isset($productData[$groupName])) {
                        $result[$groupName] = [];
                    }

                    $result[$groupName][] = match ($group->getDisplayType()) {
                        'color' => $propertyName . ($property->getColorHexCode() ? ';' . $property->getColorHexCode() : ''),
                        'media' => $propertyName . ($property->getMedia() ? ';' . $property->getMedia()->getPath() : ''),
                        default => $propertyName,
                    };
                }
            }

            // Convert single-item arrays to strings
            foreach ($result as $groupName => $values) {
                if (count($values) === 1) {
                    $result[$groupName] = $values[0];
                }
            }
        }

        return $result;
    }

    private function getPrices(array $item): array
    {
        $result = [];
        $defaultPrice = $item['price'][0];

        $result['defaultPricing'] = [
            'net' => $defaultPrice['net'],
            'gross' => $defaultPrice['gross']
        ];

        if($defaultPrice['percentage']) {
            $result['defaultPricing']['percentage'] = [
                'net' => $defaultPrice['percentage']['net'],
                'gross' => $defaultPrice['percentage']['gross']
            ];

            $result['defaultPricing']['inSale'] = $defaultPrice['percentage']['net'] > 0 && $defaultPrice['percentage']['gross'] > 0;
        }

        if($defaultPrice['listPrice']) {
            $result['defaultPricing']['listPrice'] = [
                'net' => $defaultPrice['listPrice']['net'],
                'gross' => $defaultPrice['listPrice']['gross']
            ];
        }

        if($defaultPrice['regulationPrice']) {
            $result['defaultPricing']['regulationPrice'] = [
                'net' => $defaultPrice['regulationPrice']['net'],
                'gross' => $defaultPrice['regulationPrice']['gross']
            ];
        }

        foreach($item['prices'] as $advancedPrices)
        {
            $prices = $advancedPrices['price'];
            $ruleId = $advancedPrices['ruleId'];

            foreach($prices as $price)
            {
                $result['advancedPricing'][$ruleId] = [
                    'net' => $price['net'],
                    'gross' => $price['gross']
                ];

                if($price['listPrice']) {
                    $result['advancedPricing'][$ruleId]['listPrice'] = [
                        'net' => $price['listPrice']['net'],
                        'gross' => $price['listPrice']['gross']
                    ];
                }

                if($price['percentage']) {
                    $result['advancedPricing'][$ruleId]['percentage'] = [
                        'net' => $price['percentage']['net'],
                        'gross' => $price['percentage']['gross']
                    ];
                }

                if($price['regulationPrice']) {
                    $result['advancedPricing'][$ruleId]['regulationPrice'] = [
                        'net' => $price['regulationPrice']['net'],
                        'gross' => $price['regulationPrice']['gross']
                    ];
                }
            }
        }

        return $result;
    }

    private function skipParentProduct(array $item): bool
    {
        if (
            (str_contains($item['productNumber'], self::PARENT_PRODUCT_IDICATOR) && $item['childCount'] > 0)
            || $item['stock'] <= 0 && $item['isCloseout'] === true
        ) {
            return true;
        }

        return false;
    }
}