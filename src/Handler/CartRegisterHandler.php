<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CartRegisterHandler implements CartRegisterHandlerInterface
{
    public function __construct(
        private EcommerceInterface $ecommerceApi,
        private CustomerRegisterHandlerInterface $customerRegisterHandler,
        private RouterInterface $router,
        private OrderTokenAssignerInterface $orderTokenAssigner,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private EventDispatcherInterface $eventDispatcher,
        private bool $enabled
    ) {
    }

    public function register(OrderInterface $order, bool $createOnly = false): array
    {
        if (!$this->enabled) {
            return [];
        }

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        $channel = $order->getChannel();

        if (
            null == $customer ||
            null == $channel ||
            count($order->getItems()) == 0
        ) {
            return [];
        }

        /** @var string $storeId */
        $storeId = $channel->getCode();
        $cartId = (string) $order->getId();

        $response = $this->ecommerceApi->getCart($storeId, $cartId);
        $isNew = !isset($response['id']);

        if (false === $isNew && true === $createOnly) {
            return [];
        }

        $response = $this->customerRegisterHandler->register($customer, $channel, false, $createOnly);

        if (!isset($response['id'])) {
            return [];
        }

        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);
        $this->entityManager->flush();

        $context = $this->router->getContext();
        $context->setHost($channel->getHostname() ?? '');
        $continuePurchaseUrl = $this->router->generate('odiseo_sylius_mailchimp_plugin_shop_continue_cart_purchase', [
            '_locale' => $order->getLocaleCode() ?? 'en',
            'tokenValue' => $order->getTokenValue(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $data = [
            'id' => $cartId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'checkout_url' => $continuePurchaseUrl,
            'currency_code' => $order->getCurrencyCode() ?? 'USD',
            'order_total' => $order->getTotal() / 100,
            'tax_total' => $order->getTaxTotal() / 100,
            'lines' => [],
        ];

        if ($this->requestStack->getSession()->has('campaignId')) {
            $data['campaign_id'] = $this->requestStack->getSession()->get('campaignId');
        }

        foreach ($order->getItems() as $item) {
            $product = $item->getProduct();
            $variant = $item->getVariant();

            if (null == $product || null == $variant) {
                continue;
            }

            $data['lines'][] = [
                'id' => (string) $item->getId(),
                'product_id' => (string) $product->getId(),
                'product_variant_id' => (string) $variant->getId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getTotal() / 100,
            ];
        }

        $event = new GenericEvent($order, ['data' => $data]);
        if ($isNew) {
            $this->eventDispatcher->dispatch($event, 'mailchimp.cart.pre_add');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->addCart($storeId, $data);
        } else {
            $this->eventDispatcher->dispatch($event, 'mailchimp.cart.pre_update');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->updateCart($storeId, $cartId, $data);
        }

        return $response;
    }

    public function unregister(OrderInterface $order): array
    {
        if (!$this->enabled) {
            return [];
        }

        $orderId = (string) $order->getId();
        $channel = $order->getChannel();

        if (null == $channel) {
            return [];
        }

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getCart($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($order);

            $this->eventDispatcher->dispatch($event, 'mailchimp.cart.pre_remove');

            return $this->ecommerceApi->removeCart($storeId, $orderId);
        }

        return [];
    }
}
