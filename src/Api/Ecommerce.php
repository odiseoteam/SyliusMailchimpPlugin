<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;

final class Ecommerce extends MailChimp implements EcommerceInterface
{
    public function __construct(
        private LoggerInterface $logger,
        string $api_key,
    ) {
        parent::__construct($api_key);
    }

    public function addStore(array $data): array
    {
        $response = $this->post('ecommerce/stores', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_store: ' . json_encode($response));

        return $response;
    }

    public function getStores(): array
    {
        $response = $this->get('ecommerce/stores');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_stores: ' . json_encode($response));

        return $response;
    }

    public function getStore(string $storeId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_store: ' . json_encode($response));

        return $response;
    }

    public function updateStore(string $storeId, array $data): array
    {
        $response = $this->patch('ecommerce/stores/' . $storeId, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_store: ' . json_encode($response));

        return $response;
    }

    public function removeStore(string $storeId): array
    {
        $response = $this->delete('ecommerce/stores/' . $storeId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_store: ' . json_encode($response));

        return $response;
    }

    public function addCustomer(string $storeId, array $data): array
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/customers', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_customer: ' . json_encode($response));

        return $response;
    }

    public function getCustomers(string $storeId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/customers');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_customers: ' . json_encode($response));

        return $response;
    }

    public function getCustomer(string $storeId, string $customerId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/customers/' . $customerId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_customer: ' . json_encode($response));

        return $response;
    }

    public function updateCustomer(string $storeId, string $customerId, array $data): array
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/customers/' . $customerId, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_customer: ' . json_encode($response));

        return $response;
    }

    public function removeCustomer(string $storeId, string $customerId): array
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/customers/' . $customerId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_customer: ' . json_encode($response));

        return $response;
    }

    public function addProduct(string $storeId, array $data): array
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/products', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_product: ' . json_encode($response));

        return $response;
    }

    public function getProducts(string $storeId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/products');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_products: ' . json_encode($response));

        return $response;
    }

    public function getProduct(string $storeId, string $productId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/products/' . $productId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_product: ' . json_encode($response));

        return $response;
    }

    public function updateProduct(string $storeId, string $productId, array $data): array
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/products/' . $productId, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_product: ' . json_encode($response));

        return $response;
    }

    public function removeProduct(string $storeId, string $productId): array
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/products/' . $productId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_product: ' . json_encode($response));

        return $response;
    }

    public function addOrder(string $storeId, array $data): array
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/orders', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_order: ' . json_encode($response));

        return $response;
    }

    public function getOrders(): array
    {
        $response = $this->get('ecommerce/orders');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_orders: ' . json_encode($response));

        return $response;
    }

    public function getOrdersByStore(string $storeId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/orders');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_order_by_store: ' . json_encode($response));

        return $response;
    }

    public function getOrder(string $storeId, string $orderId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/orders/' . $orderId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_order: ' . json_encode($response));

        return $response;
    }

    public function updateOrder(string $storeId, string $orderId, array $data): array
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/orders/' . $orderId, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_order: ' . json_encode($response));

        return $response;
    }

    public function removeOrder(string $storeId, string $orderId): array
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/orders/' . $orderId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_order: ' . json_encode($response));

        return $response;
    }

    public function addCart(string $storeId, array $data): array
    {
        $response = $this->post('ecommerce/stores/' . $storeId . '/carts', $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('add_cart: ' . json_encode($response));

        return $response;
    }

    public function getCarts(string $storeId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/carts');
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_carts: ' . json_encode($response));

        return $response;
    }

    public function getCart(string $storeId, string $cartId): array
    {
        $response = $this->get('ecommerce/stores/' . $storeId . '/carts/' . $cartId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('get_cart: ' . json_encode($response));

        return $response;
    }

    public function updateCart(string $storeId, string $cartId, array $data): array
    {
        $response = $this->patch('ecommerce/stores/' . $storeId . '/carts/' . $cartId, $data);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('update_cart: ' . json_encode($response));

        return $response;
    }

    public function removeCart(string $storeId, string $cartId): array
    {
        $response = $this->delete('ecommerce/stores/' . $storeId . '/carts/' . $cartId);
        if ($response === false) {
            $response = [];
        }

        $this->logger->info('remove_cart: ' . json_encode($response));

        return $response;
    }
}
