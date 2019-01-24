<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\Routing\RouterInterface;

final class CartRegisterHandler implements CartRegisterHandlerInterface
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
     * @var OrderTokenAssignerInterface
     */
    private $orderTokenAssigner;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param RouterInterface $router
     * @param OrderTokenAssignerInterface $orderTokenAssigner
     * @param EntityManagerInterface $entityManager
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     * @param bool $enabled
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        RouterInterface $router,
        OrderTokenAssignerInterface $orderTokenAssigner,
        EntityManagerInterface $entityManager,
        bool $enabled
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->router = $router;
        $this->orderTokenAssigner = $orderTokenAssigner;
        $this->entityManager = $entityManager;
        $this->customerRegisterHandler = $customerRegisterHandler;
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
        $cartId = (string) $order->getId();

        $response = $this->ecommerceApi->getCart($storeId, $cartId);
        $isNew = !isset($response['id']);

        // Do nothing if the cart exists
        if (false === $isNew && true === $createOnly) {
            return false;
        }

        // Registering the customer to ensure that exist on Mailchimp
        $response = $this->customerRegisterHandler->register($customer, $channel, false, $createOnly);

        if (!isset($response['id']) && $response !== false) {
            return false;
        }
    
        // Assigning the token value to the order
        $this->orderTokenAssigner->assignTokenValueIfNotSet($order);
        $this->entityManager->flush();

        // Creating continue purchase url
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname());
        $continuePurchaseUrl = $this->router->generate('odiseo_sylius_mailchimp_plugin_continue_cart_purchase', [
            '_locale' => $order->getLocaleCode() ?: 'en',
            'tokenValue' => $order->getTokenValue(),
        ], RouterInterface::ABSOLUTE_URL);
    
        $data = [
            'id' => $cartId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'checkout_url' => $continuePurchaseUrl,
            'currency_code' => $order->getCurrencyCode() ?: 'USD',
            'order_total' => $order->getTotal() / 100,
            'tax_total' => $order->getTaxTotal() / 100,
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
            $response = $this->ecommerceApi->addCart($storeId, $data);
        } else {
            $response = $this->ecommerceApi->updateCart($storeId, $cartId, $data);
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

        $response = $this->ecommerceApi->getCart($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeCart($storeId, $orderId);
        }

        return false;
    }
}
