<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Controller\Action;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class ContinueCartPurchaseAction
{
    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        CartStorageInterface $cartStorage,
        OrderRepositoryInterface $orderRepository,
        RouterInterface $router
    ) {
        $this->cartStorage = $cartStorage;
        $this->orderRepository = $orderRepository;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $tokenValue = $request->get('tokenValue');
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy([
            'tokenValue' => $tokenValue,
        ]);

        Assert::notNull($order);

        // If the order state is paid
        if (OrderPaymentStates::STATE_PAID === $order->getPaymentState()) {
            return new RedirectResponse($this->router->generate('sylius_shop_order_show', [
                'tokenValue' => $order->getTokenValue(),
            ]));
        }

        // If the order is in state cart with a valid token value
        if (OrderInterface::STATE_CART !== $order->getState() && $order->getTokenValue()) {
            return new RedirectResponse($this->router->generate('sylius_shop_order_pay', [
                'tokenValue' => $order->getTokenValue(),
            ]));
        }

        $this->cartStorage->setForChannel($order->getChannel(), $order);

        return new RedirectResponse($this->router->generate('sylius_shop_checkout_start'));
    }
}
