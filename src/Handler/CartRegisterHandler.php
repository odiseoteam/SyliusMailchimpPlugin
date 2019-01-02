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
     * @param EcommerceInterface $ecommerceApi
     * @param RouterInterface $router
     * @param OrderTokenAssignerInterface $orderTokenAssigner
     * @param EntityManagerInterface $entityManager
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        RouterInterface $router,
        OrderTokenAssignerInterface $orderTokenAssigner,
        EntityManagerInterface $entityManager
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->router = $router;
        $this->orderTokenAssigner = $orderTokenAssigner;
        $this->entityManager = $entityManager;
        $this->customerRegisterHandler = $customerRegisterHandler;
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
        $cartId = (string) $order->getId();

        // Registering the customer to ensure that exist on Mailchimp
        $response = $this->customerRegisterHandler->register($customer, $channel);

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
            '_locale' => $order->getLocaleCode()?:'en',
            'tokenValue' => $order->getTokenValue()
        ], RouterInterface::ABSOLUTE_URL);

        $response = $this->ecommerceApi->getCart($storeId, $cartId);
        $isNew = !isset($response['id']);

        $data = [
            'id' => $cartId,
            'customer' => [
                'id' => (string) $customer->getId(),
            ],
            'checkout_url' => $continuePurchaseUrl,
            'currency_code' => $order->getCurrencyCode()?:'USD',
            'order_total' => $order->getTotal()/100,
            'tax_total' => $order->getTaxTotal()/100,
            'lines' => [],
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
            $response = $this->ecommerceApi->addCart($storeId, $data);
        } else {
            $response = $this->ecommerceApi->updateCart($storeId, $cartId, $data);
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

        $response = $this->ecommerceApi->getCart($storeId, $orderId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeCart($storeId, $orderId);
        }

        return false;
    }
}
