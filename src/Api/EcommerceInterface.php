<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface EcommerceInterface
{
    /**
     * @param array $data
     * @return array|false
     */
    public function addStore(array $data): array;

    /**
     * @return array|false
     */
    public function getStores(): array;

    /**
     * @param string $storeId
     * @return array|false
     */
    public function getStore(string $storeId): array;

    /**
     * @param string $storeId
     * @param array $data
     * @return array|false
     */
    public function updateStore(string $storeId, array $data): array;

    /**
     * @param string $storeId
     * @return array|false
     */
    public function removeStore(string $storeId): array;

    /**
     * @param string $storeId
     * @param array $data
     * @return array|false
     */
    public function addCustomer(string $storeId, array $data): array;

    /**
     * @param string $storeId
     * @return array|false
     */
    public function getCustomers(string $storeId): array;

    /**
     * @param string $storeId
     * @param string $customerId
     * @return array|false
     */
    public function getCustomer(string $storeId, string $customerId): array;

    /**
     * @param string $storeId
     * @param string $customerId
     * @param array $data
     * @return array|false
     */
    public function updateCustomer(string $storeId, string $customerId, array $data): array;

    /**
     * @param string $storeId
     * @param string $customerId
     * @return array|false
     */
    public function removeCustomer(string $storeId, string $customerId): array;

    /**
     * @param string $storeId
     * @param array $data
     * @return array|false
     */
    public function addProduct(string $storeId, array $data): array;

    /**
     * @param string $storeId
     * @param string $productId
     * @return array|false
     */
    public function getProduct(string $storeId, string $productId): array;

    /**
     * @param string $storeId
     * @param string $productId
     * @param array $data
     * @return array|false
     */
    public function updateProduct(string $storeId, string $productId, array $data): array;

    /**
     * @param string $storeId
     * @param string $productId
     * @return array|false
     */
    public function removeProduct(string $storeId, string $productId): array;

    /**
     * @param string $storeId
     * @param array $data
     * @return array|false
     */
    public function addOrder(string $storeId, array $data): array;

    /**
     * @return array|false
     */
    public function getOrders(): array;

    /**
     * @param string $storeId
     * @return array|false
     */
    public function getOrdersByStore(string $storeId): array;

    /**
     * @param string $storeId
     * @param string $orderId
     * @return array|false
     */
    public function getOrder(string $storeId, string $orderId): array;

    /**
     * @param string $storeId
     * @param string $orderId
     * @param array $data
     * @return array|false
     */
    public function updateOrder(string $storeId, string $orderId, array $data): array;

    /**
     * @param string $storeId
     * @param string $orderId
     * @return array|false
     */
    public function removeOrder(string $storeId, string $orderId): array;

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function addCart(string $storeId, array $data): array;

    /**
     * @param string $storeId
     * @return array|false
     */
    public function getCarts(string $storeId): array;

    /**
     * @param string $storeId
     * @param string $cartId
     * @return array|false
     */
    public function getCart(string $storeId, string $cartId): array;

    /**
     * @param string $storeId
     * @param string $cartId
     * @param array $data
     * @return array|false
     */
    public function updateCart(string $storeId, string $cartId, array $data): array;

    /**
     * @param string $storeId
     * @param string $cartId
     * @return array|false
     */
    public function removeCart(string $storeId, string $cartId): array;
}
