<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;

class Ecommerce extends MailChimp implements EcommerceInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param $api_key
     * @param LoggerInterface $logger
     *
     * @throws \Exception
     */
    public function __construct(
        $api_key,
        LoggerInterface $logger
    ) {
        parent::__construct($api_key);

        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function addStore(array $data)
    {
        $response = $this->post('ecommerce/stores', $data);

        $this->logger->info('add_store: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        $response = $this->get('ecommerce/stores');

        $this->logger->info('get_stores: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore(string $storeId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId);

        $this->logger->info('get_store: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStore(string $storeId, array $data)
    {
        $response = $this->patch('ecommerce/stores/' . $storeId, $data);

        $this->logger->info('update_store: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(string $storeId)
    {
        $response = $this->delete('ecommerce/stores/' . $storeId);

        $this->logger->info('remove_store: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomer(string $storeId, array $data)
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/customers', $data);

        $this->logger->info('add_customer: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomers(string $storeId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/customers');

        $this->logger->info('get_customers: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(string $storeId, string $customerId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/customers/' . $customerId);

        $this->logger->info('get_customer: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomer(string $storeId, string $customerId, array $data)
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/customers/' . $customerId, $data);

        $this->logger->info('update_customer: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomer(string $storeId, string $customerId)
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/customers/' . $customerId);

        $this->logger->info('remove_customer: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addProduct(string $storeId, array $data)
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/products', $data);

        $this->logger->info('add_product: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts(string $storeId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/products');

        $this->logger->info('get_products: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(string $storeId, string $productId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/products/' . $productId);

        $this->logger->info('get_product: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateProduct(string $storeId, string $productId, array $data)
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/products/' . $productId, $data);

        $this->logger->info('update_product: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct(string $storeId, string $productId)
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/products/' . $productId);

        $this->logger->info('remove_product: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder(string $storeId, array $data)
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/orders', $data);

        $this->logger->info('add_order: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders()
    {
        $response = $this->get('ecommerce/orders');

        $this->logger->info('get_orders: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrdersByStore(string $storeId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/orders');

        $this->logger->info('get_order_by_store: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(string $storeId, string $orderId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/orders/' . $orderId);

        $this->logger->info('get_order: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOrder(string $storeId, string $orderId, array $data)
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/orders/' . $orderId, $data);

        $this->logger->info('update_order: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrder(string $storeId, string $orderId)
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/orders/' . $orderId);

        $this->logger->info('remove_order: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addCart(string $storeId, array $data)
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/carts', $data);

        $this->logger->info('add_cart: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarts(string $storeId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/carts');

        $this->logger->info('get_carts: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(string $storeId, string $cartId)
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/carts/' . $cartId);

        $this->logger->info('get_cart: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateCart(string $storeId, string $cartId, array $data)
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/carts/' . $cartId, $data);

        $this->logger->info('update_cart: '.json_encode($response));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCart(string $storeId, string $cartId)
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/carts/' . $cartId);

        $this->logger->info('remove_cart: '.json_encode($response));

        return $response;
    }
}
