<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class CartRegisterHandler
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param RouterInterface $router
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        RouterInterface $router
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->router = $router;
    }

    /**
     * @param OrderInterface $order
     */
    public function register(OrderInterface $order)
    {
        $channel = $order->getChannel();
        $storeId = $channel->getCode();

        $cartId = (string)$order->getId();

        $routeName = $this->router->generate(
            'sylius_shop_checkout_start',
            [],
            UrlGenerator::ABSOLUTE_URL
        );

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

        $response = $this->ecommerceApi->getCart($storeId, $cartId);

        Assert::keyExists($response, 'status');

        if ($response['status'] === Response::HTTP_NOT_FOUND) {
            /** @var CustomerInterface $customer */
            if (null === $customer = $order->getCustomer()) {
                return;
            }

            $data = [
                'id' => $cartId,
                'customer' => [
                    'id' => (string)$customer->getId(),
                    'email_address' => $customer->getEmail(),
                    'opt_in_status' => false,
                    'first_name' => $customer->getFirstName() ? $customer->getFirstName() : '-',
                    'last_name' => $customer->getLastName() ? $customer->getLastName() : '-'
                ],
                'currency_code' => 'USD',
                'order_total' => $order->getTotal(),
                'checkout_url' => $routeName,
                'lines' => $lines,
            ];

            $this->ecommerceApi->addCart($storeId, $data);
        } else {
            $data = [
                'order_total' => $order->getTotal(),
                'checkout_url' => $routeName,
                'lines' => $lines
            ];

            $this->ecommerceApi->updateCart($storeId, $cartId, $data);
        }
    }
}
