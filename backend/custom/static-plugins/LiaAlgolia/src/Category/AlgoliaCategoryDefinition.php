<?php declare(strict_types=1);

namespace Lia\Algolia\Category;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Lia\Algolia\Framework\AbstractAlgoliaDefinition;
use Lia\Algolia\Framework\AlgoliaIndexingUtils;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class AlgoliaCategoryDefinition extends AbstractAlgoliaDefinition
{
    const ASSOCIATION = [
        'seoUrls'
    ];

    public function __construct(
        private readonly EntityDefinition $definition,
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
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new ContainsFilter('path', $navigationCategoryId));

        foreach(self::ASSOCIATION as $association) {
            $criteria->addAssociation($association);
        }

        $categoriesResult = $this->categoryRepository->search($criteria, $context);
        $data = json_decode(json_encode($categoriesResult->getElements()), true);;

        if (empty($data)) {
            return [];
        }

        $categoryCriteria = new Criteria();
        $categoryCriteria->addFilter(
            new EqualsFilter('id', $navigationCategoryId)
        );

        $navigationCategoryData = $this->categoryRepository->search($categoryCriteria, $context)->first();

        $navigationCategoryName = $navigationCategoryData->getName();

        $categories = [];

        foreach ($data as $id => $item) {
            $hierarchicalCategories = $this->buildHierarchicalCategories([$item['breadcrumb']], $navigationCategoryName);
            $seoUrl = $item['seoUrls'][0]['seoPathInfo'] ?? '#';

            $categories[$id] = [
                'id' => $id,
                'name' => AlgoliaIndexingUtils::getTranslation($item, 'name'),
                'url' => $seoUrl
            ];

            if(count($hierarchicalCategories) > 0) {
                $categories[$id]['hierarchicalCategories'] = $hierarchicalCategories;
            }
        }

        return $categories;
    }
}