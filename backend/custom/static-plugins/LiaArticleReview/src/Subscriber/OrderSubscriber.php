<?php declare(strict_types=1);

namespace Lia\ArticleReview\Subscriber;

use DateTime;
use Lia\ArticleReview\LiaArticleReview;
use Lia\ArticleReview\Service\ConfigService;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryDefinition;
use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    private ConfigService $configService;

    private EntityRepository $orderRepository;

    public function __construct(
        ConfigService $configService,
        EntityRepository $orderRepository
    )
    {
        $this->configService = $configService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OrderEvents::ORDER_DELIVERY_WRITTEN_EVENT => 'onOrderUpdate',
        ];
    }

    /**
     * @throws \Exception
     */
    public function onOrderUpdate(EntityWrittenEvent $event): void
    {
        $deliveryState = $this->configService->getDeliveryState();

        if(!$deliveryState) {
            return;
        }

        foreach ($event->getWriteResults() as $result) {
            $payload = $result->getPayload();

            if(!$payload) {
                continue;
            }

            $stateId = $payload['stateId'];

            if($result->getEntityName() !== OrderDeliveryDefinition::ENTITY_NAME) {
                continue;
            }

            if($result->getOperation() !== 'update') {
                $orderId = $payload['orderId'];
                $now = new DateTime(date(Defaults::STORAGE_DATE_FORMAT));

                $orderCriteria = new Criteria();
                $orderCriteria->addFilter(new EqualsFilter('id', $orderId));

                $order = $this->orderRepository->search($orderCriteria, $event->getContext())->first();

                if(!$order) {
                    continue;
                }

                $customFields = $order->getCustomFields() ?? [];

                if(!isset($customFields[LiaArticleReview::BUNDLE_NAME])) {
                    continue;
                }

                if($stateId === $deliveryState) {
                    $customFields = array_merge_recursive([
                        LiaArticleReview::BUNDLE_NAME => [
                            'shipped_at' => date(Defaults::STORAGE_DATE_TIME_FORMAT, $now->getTimestamp())
                        ]
                    ], $customFields);
                } else {
                    if(isset($customFields[LiaArticleReview::BUNDLE_NAME]['shipped_at'])) {
                        unset($customFields[LiaArticleReview::BUNDLE_NAME]['shipped_at']);
                    }
                }

                $this->orderRepository->update([[
                    'id' => $orderId,
                    'customFields' => $customFields
                ]], $event->getContext());
            }
        }
    }
}