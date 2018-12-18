<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class OrderRegisterHandler
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @param EcommerceInterface $ecommerceApi
     */
    public function __construct(
        EcommerceInterface $ecommerceApi
    ) {
        $this->ecommerceApi = $ecommerceApi;
    }

    /**
     * @param OrderInterface $order
     */
    public function register(OrderInterface $order)
    {
        $channel = $order->getChannel();
        $storeId = $channel->getCode();

        $orderId = (string)$order->getId();

        $lines = [];
        $items = $order->getItems();
        foreach ($items as $item) {
            $lines[] = [
                'id' => (string)$item->getId(),
                'product_id' => (string)$item->getProduct()->getId(),
                'product_variant_id' => (string)$item->getVariant()->getId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getTotal()
            ];
        }

        $response = $this->ecommerceApi->getOrder($storeId, $orderId);

        Assert::keyExists($response, 'status');

        if ($response['status'] === Response::HTTP_NOT_FOUND) {
            /** @var CustomerInterface $customer */
            if (null === $customer = $order->getCustomer()) {
                return;
            }

            $data = [
                'id' => $orderId,
                'customer' => [
                    'id' => (string)$customer->getId(),
                    'email_address' => $customer->getEmail(),
                    'opt_in_status' => false,
                    'first_name' => $customer->getFirstName() ? $customer->getFirstName() : '-',
                    'last_name' => $customer->getLastName() ? $customer->getLastName() : '-'
                ],
                'currency_code' => 'USD',
                'order_total' => $order->getTotal(),
                'lines' => $lines
            ];

            $this->ecommerceApi->addOrder($storeId, $data);
        } else {
            $data = [
                'order_total' => $order->getTotal(),
                'lines' => $lines
            ];

            $this->ecommerceApi->updateOrder($storeId, $orderId, $data);
        }
    }
}
