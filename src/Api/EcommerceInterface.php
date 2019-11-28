<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface EcommerceInterface
{
    /**
     * @param array $data
     */
    public function addStore(array $data);

    public function getStores();

    /**
     * @param string $storeId
     */
    public function getStore(string $storeId);

    /**
     * @param string $storeId
     * @param array $data
     */
    public function updateStore(string $storeId, array $data);

    /**
     * @param string $storeId
     */
    public function removeStore(string $storeId);

    /**
     * @param string $storeId
     * @param array $data
     */
    public function addCustomer(string $storeId, array $data);

    /**
     * @param string $storeId
     */
    public function getCustomers(string $storeId);

    /**
     * @param string $storeId
     * @param string $customerId
     */
    public function getCustomer(string $storeId, string $customerId);

    /**
     * @param string $storeId
     * @param string $customerId
     * @param array $data
     */
    public function updateCustomer(string $storeId, string $customerId, array $data);

    /**
     * @param string $storeId
     * @param string $customerId
     */
    public function removeCustomer(string $storeId, string $customerId);

    /**
     * @param string $storeId
     * @param array $data
     */
    public function addProduct(string $storeId, array $data);

    /**
     * @param string $storeId
     */
    public function getProducts(string $storeId);

    /**
     * @param string $storeId
     * @param string $productId
     */
    public function getProduct(string $storeId, string $productId);

    /**
     * @param string $storeId
     * @param string $productId
     * @param array $data
     */
    public function updateProduct(string $storeId, string $productId, array $data);

    /**
     * @param string $storeId
     * @param string $productId
     */
    public function removeProduct(string $storeId, string $productId);

    /**
     * @param string $storeId
     * @param array $data
     */
    public function addOrder(string $storeId, array $data);

    public function getOrders();

    /**
     * @param string $storeId
     */
    public function getOrdersByStore(string $storeId);

    /**
     * @param string $storeId
     * @param string $orderId
     */
    public function getOrder(string $storeId, string $orderId);

    /**
     * @param string $storeId
     * @param string $orderId
     * @param array $data
     */
    public function updateOrder(string $storeId, string $orderId, array $data);

    /**
     * @param string $storeId
     * @param string $orderId
     */
    public function removeOrder(string $storeId, string $orderId);

    /**
     * @param string $storeId
     * @param array $data
     */
    public function addCart(string $storeId, array $data);

    /**
     * @param string $storeId
     */
    public function getCarts(string $storeId);

    /**
     * @param string $storeId
     * @param string $cartId
     */
    public function getCart(string $storeId, string $cartId);

    /**
     * @param string $storeId
     * @param string $cartId
     * @param array $data
     */
    public function updateCart(string $storeId, string $cartId, array $data);

    /**
     * @param string $storeId
     * @param string $cartId
     */
    public function removeCart(string $storeId, string $cartId);
}
