<?php declare(strict_types=1);

namespace Lia\Algolia\Subscriber;

use Lia\Algolia\Exception\AlgoliaException;
use Lia\Algolia\Framework\AlgoliaRegistry;
use Lia\Algolia\Framework\Indexing\AlgoliaIndexer;
use Lia\Algolia\Service\SalesChannelService;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

class ProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AlgoliaRegistry $algoliaRegistry,
        private readonly AlgoliaIndexer $algoliaIndexer,
        private readonly SalesChannelService $salesChannelService,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ProductEvents::PRODUCT_WRITTEN_EVENT => 'onProductWrittenEvent',
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function onProductWrittenEvent(EntityWrittenEvent $event)
    {
        $writeResults = $event->getWriteResults();

        $entityName = $event->getEntityName();
        $definition = $this->algoliaRegistry->get($entityName);

        if (!$definition) {
            throw AlgoliaException::definitionNotFound($entityName);
        }

        if($entityName != 'product')
        {
            return [];
        }

        if(!$writeResults) {
            return [];
        }

        $productIds = array_map(function ($writeResult) {
            return $writeResult->getPrimaryKey();
        }, $writeResults);

        /* @var $salesChannels SalesChannelEntity[] */
        $salesChannels = $this->salesChannelService->getAllSalesChannels();

        foreach ($salesChannels as $salesChannel) {
            $languages = $salesChannel->getLanguages()->getElements();

            foreach ($languages as $language) {
                $this->algoliaIndexer->updateIds($productIds, $definition->getEntityDefinition(), $language, $salesChannel);
            }
        }
    }
}