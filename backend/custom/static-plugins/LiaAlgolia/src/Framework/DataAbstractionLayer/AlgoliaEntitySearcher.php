<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\DataAbstractionLayer;

use Algolia\AlgoliaSearch\SearchClient;
use Lia\Algolia\Exception\AlgoliaException;
use Lia\Algolia\Service\AlgoliaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

class AlgoliaEntitySearcher implements EntitySearcherInterface
{
    final public const RESULT_STATE = 'loaded-by-algolia';

    public function __construct(
        private readonly EntitySearcherInterface $decorated,
        private readonly AlgoliaService $algoliaService,
        private readonly AbstractAlgoliaSearchHydrator $hydrator
    )
    {
    }

    public function search(EntityDefinition $definition, Criteria $criteria, Context $context): IdSearchResult
    {
        //return $this->decorated->search($definition, $criteria, $context);

        if (!$this->algoliaService->allowSearch($definition, $context, $criteria)) {
            return $this->decorated->search($definition, $criteria, $context);
        }

        if ($criteria->getLimit() === 0) {
            return new IdSearchResult(0, [], $criteria, $context);
        }

        try {
            $indexName = $this->algoliaService->getIndexName($definition, $context->getSource()->getSalesChannelId(), $context->getLanguageId());
            $search = $this->algoliaService->createClient($context->getSource()->getSalesChannelId());
            $index = $search->initIndex($indexName);

            $algoliaQuery = $this->algoliaService->getFilters($definition, $criteria, $context);

            $limit = $criteria->getLimit();
            if ($limit !== null) {
                $algoliaQuery['hitsPerPage'] = $limit;
            }
            $algoliaQuery['offset'] = $criteria->getOffset();
            $algoliaQuery['length'] = $limit;

            $result = $index->search('', $algoliaQuery);

            $result = $this->hydrator->hydrate($definition, $criteria, $context, $result);

            $result->addState(self::RESULT_STATE);

            return $result;
        } catch (\Throwable $e) {
            if ($e instanceof AlgoliaException && $e->getErrorCode() === AlgoliaException::EMPTY_QUERY) {
                return new IdSearchResult(0, [], $criteria, $context);
            }

            $this->algoliaService->logAndThrowException($e);

            return $this->decorated->search($definition, $criteria, $context);
        }
    }
}