<?php declare(strict_types=1);

namespace Lia\ArticleReview\Subscriber;

use Lia\ArticleReview\LiaArticleReview;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CheckoutSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    private EntityRepository $orderRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityRepository $orderRepository
    )
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
          CheckoutOrderPlacedEvent::class => 'onOrderPlaced'
        ];
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $agreeRatingConsent = $this->checkBoolean($this->requestStack->getCurrentRequest()->get('agreeRatingConsent'));
        $order = $event->getOrder();

        $customFields = $order->getCustomFields() ?? [];

        $customFields = array_merge([
            LiaArticleReview::BUNDLE_NAME => [
                'agreeRatingConsent' => $agreeRatingConsent
            ]
        ], $customFields);

        $this->orderRepository->update([[
            'id' => $order->getId(),
            'customFields' => $customFields
        ]], $event->getContext());
    }

    private function checkBoolean(string|null $value = null): bool
    {
        return match ($value) {
            'on' => true,
            default => false
        };
    }
}