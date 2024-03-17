<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Controller\Action;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class ContinueCartPurchaseAction
{
    public function __construct(
        private CartStorageInterface $cartStorage,
        private OrderRepositoryInterface $orderRepository,
        private RouterInterface $router
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $tokenValue = $request->query->get('tokenValue');

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy([
            'tokenValue' => $tokenValue,
        ]);

        Assert::notNull($order);

        if (OrderPaymentStates::STATE_PAID === $order->getPaymentState()) {
            return new RedirectResponse($this->router->generate('sylius_shop_order_show', [
                'tokenValue' => $order->getTokenValue(),
            ]));
        }

        if (BaseOrderInterface::STATE_CART !== $order->getState() && null !== $order->getTokenValue()) {
            return new RedirectResponse($this->router->generate('sylius_shop_order_pay', [
                'tokenValue' => $order->getTokenValue(),
            ]));
        }

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $this->cartStorage->setForChannel($channel, $order);

        return new RedirectResponse($this->router->generate('sylius_shop_checkout_start'));
    }
}
