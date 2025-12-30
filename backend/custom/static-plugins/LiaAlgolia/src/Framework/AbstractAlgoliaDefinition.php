<?php declare(strict_types=1);

namespace Lia\Algolia\Framework;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

abstract class AbstractAlgoliaDefinition
{
    final public const KEYWORD_FIELD = [
        'type' => 'keyword',
        'normalizer' => 'sw_lowercase_normalizer',
    ];

    final public const BOOLEAN_FIELD = ['type' => 'boolean'];

    final public const FLOAT_FIELD = ['type' => 'double'];

    final public const INT_FIELD = ['type' => 'long'];

    final public const SEARCH_FIELD = [
        'fields' => [
            'search' => ['type' => 'text'],
            'ngram' => ['type' => 'text', 'analyzer' => 'sw_ngram_analyzer'],
        ],
    ];

    abstract public function getEntityDefinition(): EntityDefinition;

    /**
     * @return array{_source?: array{includes: string[]}, properties: array<mixed>}
     */
    abstract public function getMapping(Context $context): array;

    /**
     * Can be used to define custom queries to define the data to be indexed.
     */
    public function getIterator(): ?IterableQuery
    {
        return null;
    }

    /**
     * @param array<string> $ids
     *
     * @return array<string, array<string, mixed>>
     */
    public function fetch(array $ids, Context $context, string $salesChannelId = '', string $navigationCategoryId = ''): array
    {
        return [];
    }

    abstract public function buildTermQuery(Context $context, Criteria $criteria);

    /**
     * @return array<string, mixed>
     */
    protected static function getTextFieldConfig(): array
    {
        return self::KEYWORD_FIELD + self::SEARCH_FIELD;
    }

    public function buildHierarchicalCategories(array $breadcrumbs, string $navigationCategoryName): array
    {
        $categories = [];

        $_breadcrumbs = $this->filterBreadcrumbsByNavigationCategoryName($breadcrumbs, $navigationCategoryName);

        foreach ($_breadcrumbs as $breadcrumb) {
            $path = array_slice($breadcrumb, 1);
            $fullPath = [];

            foreach ($path as $level => $category) {
                $fullPath[] = $category;
                $lvlKey = 'lvl' . $level;
                $joined = implode(' > ', $fullPath);

                if (!isset($categories[$lvlKey])) {
                    $categories[$lvlKey] = [];
                }

                if (!in_array($joined, $categories[$lvlKey])) {
                    $categories[$lvlKey][] = $joined;
                }
            }
        }

        return $categories;
    }

    private function filterBreadcrumbsByNavigationCategoryName($breadcrumbs, $navigationCategoryName): array
    {
        return array_filter($breadcrumbs, function($item) use ($navigationCategoryName) {
            return $item[0] === $navigationCategoryName;
        });
    }
}