<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use DateTime;
use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class OrderRegisterHandler implements OrderRegisterHandlerInterface
{
    public function __construct(
        private EcommerceInterface $ecommerceApi,
        private CustomerRegisterHandlerInterface $customerRegisterHandler,
        private RouterInterface $router,
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
        $orderId = (string) $order->getId();

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (false === $isNew && true === $createOnly) {
            return [];
        }

        $response = $this->customerRegisterHandler->register($customer, $channel, false, $createOnly);

        if (!isset($response['id'])) {
            return [];
        }

        $context = $this->router->getContext();
        $context->setHost($channel->getHostname() ?? '');
        $orderShowUrl = $this->router->generate('sylius_shop_order_show', [
            '_locale' => $order->getLocaleCode() ?? 'en',
            'tokenValue' => $order->getTokenValue(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $lastCompletedPayment = $order->getLastPayment(PaymentInterface::STATE_COMPLETED);
        /** @var DateTime $orderCompletedDate */
        $orderCompletedDate = $lastCompletedPayment !== null ?
            $lastCompletedPayment->getUpdatedAt() : $order->getUpdatedAt()
        ;

        /** @var AddressInterface $shippingAddress */
        $shippingAddress = $order->getShippingAddress();
        /** @var AddressInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        $data = [
            'id' => $orderId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'financial_status' => 'paid',
            'currency_code' => $order->getCurrencyCode() ?? 'USD',
            'order_total' => $order->getTotal() / 100,
            'order_url' => $orderShowUrl,
            'discount_total' => $order->getOrderPromotionTotal() / 100,
            'tax_total' => $order->getTaxTotal() / 100,
            'shipping_total' => $order->getShippingTotal() / 100,
            'processed_at_foreign' => $orderCompletedDate->format('c'),
            'shipping_address' => $this->getAddressData($shippingAddress),
            'billing_address' => $this->getAddressData($billingAddress),
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
            $this->eventDispatcher->dispatch($event, 'mailchimp.order.pre_add');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->addOrder($storeId, $data);

            $this->removeCart($order);
        } else {
            $this->eventDispatcher->dispatch($event, 'mailchimp.order.pre_update');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->updateOrder($storeId, $orderId, $data);
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

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($order);

            $this->eventDispatcher->dispatch($event, 'mailchimp.order.pre_remove');

            return $this->ecommerceApi->removeOrder($storeId, $orderId);
        }

        return [];
    }

    private function getAddressData(AddressInterface $address): array
    {
        return [
            'company' => $address->getCompany() ?? '',
            'address1' => $address->getStreet() ?? '',
            'city' => $address->getCity() ?? '',
            'province' => $address->getProvinceName() ?? '',
            'province_code' => $address->getProvinceCode() ?? '',
            'postal_code' => $address->getPostcode() ?? '',
            'country_code' => $address->getCountryCode() ?? '',
            'phone' => $address->getPhoneNumber() ?? '',
        ];
    }

    private function removeCart(OrderInterface $order): void
    {
        $cartId = (string) $order->getId();
        $channel = $order->getChannel();

        if (null == $channel) {
            return;
        }

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $this->ecommerceApi->removeCart($storeId, $cartId);
    }
}
