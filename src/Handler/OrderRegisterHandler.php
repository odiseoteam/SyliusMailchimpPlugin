<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

final class OrderRegisterHandler implements OrderRegisterHandlerInterface
{
    /** @var EcommerceInterface */
    private $ecommerceApi;

    /** @var CustomerRegisterHandlerInterface */
    private $customerRegisterHandler;

    /** @var RouterInterface */
    private $router;

    /** @var SessionInterface */
    private $session;

    /** @var bool */
    private $enabled;

    public function __construct(
        EcommerceInterface $ecommerceApi,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        RouterInterface $router,
        SessionInterface $session,
        bool $enabled
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->router = $router;
        $this->session = $session;
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function register(OrderInterface $order, bool $createOnly = false)
    {
        if (!$this->enabled) {
            return false;
        }

        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        $channel = $order->getChannel();

        if (
            null == $customer ||
            null == $channel ||
            count($order->getItems()) == 0
        ) {
            return false;
        }

        $storeId = $channel->getCode();
        $orderId = (string) $order->getId();

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        // Do nothing if the order exists
        if (false === $isNew && true === $createOnly) {
            return false;
        }

        // Registering the customer to ensure that exist on Mailchimp
        $response = $this->customerRegisterHandler->register($customer, $channel, false, $createOnly);

        if (!isset($response['id']) && $response !== false) {
            return false;
        }

        // Creating order show url
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname());
        $orderShowUrl = $this->router->generate('sylius_shop_order_show', [
            '_locale' => $order->getLocaleCode() ?: 'en',
            'tokenValue' => $order->getTokenValue(),
        ], RouterInterface::ABSOLUTE_URL);

        $lastCompletedPayment = $order->getLastPayment(PaymentInterface::STATE_COMPLETED);
        /** @var \DateTime $orderCompletedDate */
        $orderCompletedDate = $lastCompletedPayment?$lastCompletedPayment->getUpdatedAt():$order->getUpdatedAt();

        $data = [
            'id' => $orderId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'campaign_id' => $this->session->get('campaingId') ?: '',
            'financial_status' => 'paid',
            'currency_code' => $order->getCurrencyCode() ?: 'USD',
            'order_total' => $order->getTotal() / 100,
            'order_url' => $orderShowUrl,
            'discount_total' => $order->getOrderPromotionTotal() / 100,
            'tax_total' => $order->getTaxTotal() / 100,
            'shipping_total' => $order->getShippingTotal() / 100,
            'processed_at_foreign' => $orderCompletedDate->format('c'),
            'shipping_address' => $this->getAddressData($order->getShippingAddress()),
            'billing_address' => $this->getAddressData($order->getBillingAddress()),
            'lines' => [],
        ];

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

        if ($isNew) {
            $response = $this->ecommerceApi->addOrder($storeId, $data);

            // Unregister abandoned cart after order create
            $this->removeCart($order);
        } else {
            $response = $this->ecommerceApi->updateOrder($storeId, $orderId, $data);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister(OrderInterface $order)
    {
        if (!$this->enabled) {
            return false;
        }

        $orderId = (string) $order->getId();
        $channel = $order->getChannel();

        if (null == $channel) {
            return false;
        }

        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeOrder($storeId, $orderId);
        }

        return false;
    }

    /**
     * @param AddressInterface $address
     * @return array
     */
    private function getAddressData(AddressInterface $address): array
    {
        return [
            'company' => $address->getCompany() ?: '',
            'address1' => $address->getStreet() ?: '',
            'city' => $address->getCity() ?: '',
            'province' => $address->getProvinceName() ?: '',
            'province_code' => $address->getProvinceCode() ?: '',
            'postal_code' => $address->getPostcode() ?: '',
            'country_code' => $address->getCountryCode() ?: '',
            'phone' => $address->getPhoneNumber() ?: '',
        ];
    }

    /**
     * @param OrderInterface $order
     */
    private function removeCart(OrderInterface $order): void
    {
        $cartId = (string) $order->getId();
        $channel = $order->getChannel();

        if (null == $channel) {
            return;
        }

        $storeId = $channel->getCode();

        $this->ecommerceApi->removeCart($storeId, $cartId);
    }
}
