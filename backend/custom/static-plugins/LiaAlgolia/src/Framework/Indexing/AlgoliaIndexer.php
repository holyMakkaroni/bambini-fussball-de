<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Indexing;

use Algolia\AlgoliaSearch\Exceptions\MissingObjectId;
use Lia\Algolia\Exception\AlgoliaException;
use Lia\Algolia\Framework\AlgoliaRegistry;
use Lia\Algolia\Framework\Indexing\Event\AlgoliaIndexIteratorEvent;
use Lia\Algolia\Service\AlgoliaService;
use Lia\Algolia\Service\SalesChannelService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class AlgoliaIndexer
{
    public function __construct(
        private readonly AlgoliaService $algoliaService,
        private readonly AlgoliaRegistry $algoliaRegistry,
        private readonly IndexCreator $indexCreator,
        private readonly IteratorFactory $iteratorFactory,
        private readonly EntityRepository $localeRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MessageBusInterface $messageBus
    )
    {
    }

    public function __invoke(AlgoliaIndexingMessage $message): void
    {
        $this->handleIndexingMessage($message);
    }

    /**
     * @param array<string> $ids
     * @throws ExceptionInterface
     */
    public function updateIds(array $ids, EntityDefinition $definition, LanguageEntity $language, SalesChannelEntity $salesChannel): ?AlgoliaIndexingMessage
    {
        $context = Context::createDefaultContext();

        $localeCode = $this->getLocaleCode($language->getLocaleId(), $context);
        $index = $this->algoliaService->getIndexName($definition, $salesChannel->getId(), $language->getId());
        $indexing = new IndexingDto($ids, $index,  $definition->getEntityName(), $language->getId(), $salesChannel->getId(), $localeCode);

        // return indexing message for current offset
        $algoliaIndexMessage = new AlgoliaIndexingMessage($indexing, null, $context, $language->getId(), $salesChannel->getId(), $salesChannel->getNavigationCategoryId());

        $this->messageBus->dispatch($algoliaIndexMessage);

        return $algoliaIndexMessage;
    }

    public function iterate(LanguageEntity $language, SalesChannelEntity $salesChannel, ?IndexerOffset $offset = null, array $entities = []): ?AlgoliaIndexingMessage
    {
        if ($offset === null) {
            $offset = $this->init($entities);
        }


        return $this->createIndexingMessage($offset, $language, $salesChannel);
    }

    private function createIndexingMessage(IndexerOffset $offset, LanguageEntity $language, SalesChannelEntity $salesChannel): ?AlgoliaIndexingMessage
    {
        $algoliaIndexMessage = null;
        $definition = $this->algoliaRegistry->get((string) $offset->getDefinition());

        if (!$definition) {
            throw AlgoliaException::definitionNotFound((string) $offset->getDefinition());
        }

        $entity = $definition->getEntityDefinition()->getEntityName();

        $iterator = $this->iteratorFactory->createIterator($definition->getEntityDefinition(), $offset->getLastId(), 50);

        $event = new AlgoliaIndexIteratorEvent($definition, $iterator);
        $this->eventDispatcher->dispatch($event);

        $ids = $event->iterator->fetch();

        if (empty($ids)) {
            if (!$offset->hasNextDefinition()) {
                return null;
            }
            // increment definition offset
            $offset->selectNextDefinition();

            // reset last id to start iterator at the beginning
            $offset->setLastId(null);

            return $this->createIndexingMessage($offset, $language, $salesChannel);
        }

        // increment last id with iterator offset
        $offset->setLastId($iterator->getOffset());
        $context = Context::createDefaultContext();

        $localeCode = $this->getLocaleCode($language->getLocaleId(), $context);
        $index = $this->algoliaService->getIndexName($definition->getEntityDefinition(), $salesChannel->getId(), $language->getId());

        // return indexing message for current offset
        return new AlgoliaIndexingMessage(new IndexingDto(array_values($ids), $index, $entity, $localeCode), $offset, $context, $language->getId(), $salesChannel->getId(), $salesChannel->getNavigationCategoryId());
    }

    private function getLocaleCode(string $localeId, $context): string
    {
        $localeCriteria = new Criteria();
        $localeCriteria->addFilter(new EqualsFilter('id', $localeId));

        $locale = $this->localeRepository->search($localeCriteria, $context)->first();

        return $locale->getCode();
    }

    private function init(array $entities = []): IndexerOffset
    {
        $timestamp = new \DateTime();
        $entitiesToHandle = $this->handleEntities($entities);

        return new IndexerOffset(
            $entitiesToHandle,
            $timestamp->getTimestamp()
        );
    }

    /**
     * @throws MissingObjectId
     */
    private function handleIndexingMessage(AlgoliaIndexingMessage $message): void
    {
        $task = $message->getData();

        $ids = $task->getIds();

        $entity = $task->getEntity();

        $languageId = $message->getLanguageId();

        $salesChannelId = $message->getSalesChannelId();

        $navigationCategoryId = $message->getNavigationCategoryId();

        $localeCode = $task->getLocaleCode();

        $definition = $this->algoliaRegistry->get($entity);

        $context = $message->getContext();

        $context = $context->assign([
            'languageIdChain' => array_unique([$languageId, ...$context->getLanguageIdChain()]),
        ]);

        if (!$definition) {
            throw AlgoliaException::definitionNotFound($entity);
        }

        $data = $definition->fetch($ids, $context, $salesChannelId, $navigationCategoryId);

        $_index = $this->indexCreator->indexExists($definition->getEntityDefinition(), $salesChannelId, $languageId);
        if(!$_index->exists()) {
            $_index = $this->indexCreator->createIndex($definition->getEntityDefinition(), $salesChannelId, $languageId, $localeCode);
        }

        $_index->saveObjects($data, [
            'objectIDKey' => 'id'
        ]);
    }

    private function handleEntities(array $entities = []): iterable
    {
        if (empty($entities)) {
            return $this->algoliaRegistry->getDefinitionNames();
        }

        $registeredEntities = \is_array($this->algoliaRegistry->getDefinitionNames())
            ? $this->algoliaRegistry->getDefinitionNames()
            : iterator_to_array($this->algoliaRegistry->getDefinitionNames());

        $validEntities = array_intersect($entities, $registeredEntities);
        $unregisteredEntities = array_diff($entities, $registeredEntities);

        if (!empty($unregisteredEntities)) {
            $unregisteredEntityList = implode(', ', $unregisteredEntities);

            $exception = AlgoliaException::definitionNotFound($unregisteredEntityList);

            $this->algoliaService->logAndThrowException($exception);
        }

        return $validEntities;
    }
}