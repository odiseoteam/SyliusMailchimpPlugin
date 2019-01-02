<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Routing\RouterInterface;

final class OrderRegisterHandler implements OrderRegisterHandlerInterface
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @var CustomerRegisterHandlerInterface
     */
    private $customerRegisterHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     * @param RouterInterface $router
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        RouterInterface $router
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function register(OrderInterface $order)
    {
        /** @var CustomerInterface $customer */
        if ((null === $customer = $order->getCustomer()) || (count($order->getItems()) == 0)) {
            return false;
        }

        $channel = $order->getChannel();
        $storeId = $channel->getCode();
        $orderId = (string) $order->getId();

        // Registering the customer to ensure that exist on Mailchimp
        $response = $this->customerRegisterHandler->register($customer, $channel);

        if (!isset($response['id']) && $response !== false) {
            return false;
        }

        // Creating order show url
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname());
        $orderShowUrl = $this->router->generate('sylius_shop_order_show', [
            '_locale' => $order->getLocaleCode()?:'en',
            'tokenValue' => $order->getTokenValue()
        ], RouterInterface::ABSOLUTE_URL);

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        $data = [
            'id' => $orderId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'financial_status' => 'paid',
            'currency_code' => $order->getCurrencyCode()?:'USD',
            'order_total' => $order->getTotal()/100,
            'order_url' => $orderShowUrl,
            'discount_total' => $order->getOrderPromotionTotal()/100,
            'tax_total' => $order->getTaxTotal()/100,
            'shipping_total' => $order->getShippingTotal()/100,
            'processed_at_foreign' => $order->getLastPayment(PaymentInterface::STATE_COMPLETED)->getUpdatedAt()->format('c'),
            'shipping_address' => $this->getAddressData($order->getShippingAddress()),
            'billing_address' => $this->getAddressData($order->getBillingAddress()),
            'lines' => []
        ];

        foreach ($order->getItems() as $item) {
            $data['lines'][] = [
                'id' => (string) $item->getId(),
                'product_id' => (string) $item->getProduct()->getId(),
                'product_variant_id' => (string) $item->getVariant()->getId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getTotal()/100,
            ];
        }

        if ($isNew) {
            $response = $this->ecommerceApi->addOrder($storeId, $data);
        } else {
            $response = $this->ecommerceApi->updateOrder($storeId, $orderId, $data);
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function unregister(OrderInterface $order)
    {
        $orderId = (string) $order->getId();
        $storeId = $order->getChannel()->getCode();

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeOrder($storeId, $orderId);
        }

        return false;
    }

    /**
     * @param AddressInterface $address
     *
     * @return array
     */
    private function getAddressData(AddressInterface $address): array
    {
        return [
            'company' => $address->getCompany()?:'',
            'address1' => $address->getStreet()?:'',
            'city' => $address->getCity()?:'',
            'province' => $address->getProvinceName()?:'',
            'province_code' => $address->getProvinceCode()?:'',
            'postal_code' => $address->getPostcode()?:'',
            'country_code' => $address->getCountryCode()?:'',
            'phone' => $address->getPhoneNumber()?:'',
        ];
    }
}
