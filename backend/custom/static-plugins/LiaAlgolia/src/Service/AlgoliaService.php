<?php declare(strict_types=1);

namespace Lia\Algolia\Service;

use Algolia\AlgoliaSearch\SearchClient;
use Lia\Algolia\Factory\ClientFactory;
use Lia\Algolia\Framework\AlgoliaRegistry;
use Lia\Algolia\Framework\DataAbstractionLayer\CriteriaParser;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class AlgoliaService
{
    public function __construct(
        private readonly AlgoliaRegistry $registry,
        private readonly LoggerInterface $logger,
        private readonly CriteriaParser $parser,
        private readonly ClientFactory $clientFactory,
    )
    {
    }

    public function logAndThrowException(\Throwable $exception): bool
    {
        $this->logger->critical($exception->getMessage());

        return false;
    }

    public function allowSearch(EntityDefinition $definition, Context $context, Criteria $criteria): bool
    {
        if (!$this->isSupported($definition)) {
            return false;
        }

        return $criteria->hasState(Criteria::STATE_ELASTICSEARCH_AWARE);
    }

    public function isSupported(EntityDefinition $definition): bool
    {
        $entityName = $definition->getEntityName();

        return $this->registry->has($entityName);
    }

    public function getIndexName(EntityDefinition $definition, string $salesChannelId, string $languageId): string
    {
        return $salesChannelId .'_'. $languageId . '_' . $definition->getEntityName();
    }

    public function getFilters(EntityDefinition $definition, Criteria $criteria, Context $context): array
    {
        return $this->parser->parseCriteria($criteria);
    }

    public function createClient(string $salesChannelId): SearchClient
    {
        $clientFactory = $this->clientFactory;
        $clientFactory->setSalesChannelId($salesChannelId);

        return $clientFactory->createClient();
    }
}