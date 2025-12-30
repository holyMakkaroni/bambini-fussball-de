<?php
declare(strict_types=1);

namespace Lia\Checkout\Storefront\Subscriber;

use Shopware\Core\Framework\Routing\KernelListenerPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class StorefrontSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['preventPageLoadingFromXmlHttpRequest', KernelListenerPriorities::KERNEL_CONTROLLER_EVENT_SCOPE_VALIDATE_PRE],
            ],
        ];
    }

    public function preventPageLoadingFromXmlHttpRequest(ControllerEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_route') !== 'frontend.account.register.save') {
            return;
        }

        $event->getRequest()->attributes->set('XmlHttpRequest', true);
    }
}
