<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\DataAbstractionLayer;

use Lia\Algolia\Framework\DataAbstractionLayer\AbstractAlgoliaSearchHydrator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;

class AlgoliaEntitySearchHydrator extends AbstractAlgoliaSearchHydrator
{

    public function getDecorated(): AbstractAlgoliaSearchHydrator
    {
        throw new DecorationPatternException(self::class);
    }

    public function hydrate(EntityDefinition $definition, Criteria $criteria, Context $context, array $result): IdSearchResult
    {
        if (!isset($result['hits'])) {
            return new IdSearchResult(0, [], $criteria, $context);
        }

        $hits = $this->extractHits($result);

        $data = [];
        foreach ($hits as $hit) {
            $id = $hit['objectID'];

            $data[$id] = [
                'primaryKey' => $id,
                'data' => array_merge(
                    ['id' => $id]
                ),
            ];
        }

        $total = $this->getTotalValue($criteria, $result);

        return new IdSearchResult($total, $data, $criteria, $context);
    }

    /**
     * @param array{ hits: array{ hits: array<int, array{ inner_hits?: array{ inner?: array<mixed>}}>}} $result
     *
     * @return array<mixed>
     */
    private function extractHits(array $result): array
    {
        $records = [];
        $hits = $result['hits'];

        foreach ($hits as $hit) {
            $records[] = $hit;
        }

        return $records;
    }

    /**
     * @param array{ hits: array{ hits: array<mixed>, total?: array{ value: int } }, aggregations?: array<string, array<string, mixed>>} $result
     */
    private function getTotalValue(Criteria $criteria, array $result): int
    {
        if ($criteria->getTotalCountMode() !== Criteria::TOTAL_COUNT_MODE_EXACT) {
            return empty($result['nbHits']) ? 0 : \count($result['hits']);
        }

        return (int) ($result['nbHits'] ?? 0);
    }
}