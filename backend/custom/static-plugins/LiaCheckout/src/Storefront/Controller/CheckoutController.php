<?php
declare(strict_types=1);

namespace Lia\Checkout\Storefront\Controller;

use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Checkout\Cart\Error\PaymentMethodChangedError;
use Shopware\Storefront\Checkout\Cart\Error\ShippingMethodChangedError;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoader;
use Shopware\Storefront\Page\Checkout\Register\CheckoutRegisterPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CheckoutController extends StorefrontController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CheckoutConfirmPageLoader $confirmPageLoader,
        private readonly CheckoutRegisterPageLoader $registerPageLoader
    ) {
    }

    #[Route(path: '/checkout', name: 'frontend.checkout.confirm.page', options: ['seo' => false], defaults: [
        'XmlHttpRequest' => true,
        '_noStore' => true,
    ], methods: ['GET'])]
    #[Route(path: '/checkout', name: 'frontend.checkout.register.page', options: ['seo' => false], defaults: [
        'XmlHttpRequest' => true,
        '_noStore' => true,
    ], methods: ['GET'])]
    #[Route(path: '/checkout', name: 'frontend.checkout.page', options: ['seo' => false], defaults: [
        'XmlHttpRequest' => true,
        '_noStore' => true,
    ], methods: ['GET'])]
    public function checkoutPage(Request $request, SalesChannelContext $context, RequestDataBag $data): Response
    {
        if ($this->cartService->getCart($context->getToken(), $context)->getLineItems()->count() === 0) {
            return $this->redirectToRoute('frontend.checkout.cart.page');
        }

        try {
            $page = $this->confirmPageLoader->load($request, $context);
        } catch (CustomerNotLoggedInException) {
            $page = $this->registerPageLoader->load($request, $context);
        }

        $cart = $page->getCart();
        $cartErrors = $cart->getErrors();

        $this->addCartErrors($cart);

        if (!$request->query->getBoolean('redirected') && $this->routeNeedsReload($cartErrors)) {
            $cartErrors->clear();

            return $this->redirectToRoute(
                'frontend.checkout.cart.page',
                [...$request->query->all(), ...['redirected' => true]],
            );
        }
        $cartErrors->clear();

        return $this->renderStorefront('@Storefront/storefront/page/checkout/index.html.twig', [
            'page' => $page,
            'redirectTo' => $request->get('redirectTo', 'frontend.checkout.confirm.page'),
            'errorRoute' => $request->attributes->get('_route'),
            'data' => $data,
        ]);
    }

    private function routeNeedsReload(ErrorCollection $cartErrors): bool
    {
        foreach ($cartErrors as $error) {
            if ($error instanceof ShippingMethodChangedError || $error instanceof PaymentMethodChangedError) {
                return true;
            }
        }

        return false;
    }
}
