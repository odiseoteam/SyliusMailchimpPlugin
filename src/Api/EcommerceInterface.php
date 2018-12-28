<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

interface EcommerceInterface
{
    /**
     * @param array $data
     *
     * @return array|false
     */
    public function addStore(array $data);

    /**
     * @return array|false
     */
    public function getStores();

    /**
     * @param string $storeId
     *
     * @return array|false
     */
    public function getStore(string $storeId);

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function updateStore(string $storeId, array $data);

    /**
     * @param string $storeId
     *
     * @return array|false
     */
    public function removeStore(string $storeId);

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function addCustomer(string $storeId, array $data);

    /**
     * @param string $storeId
     *
     * @return array|false
     */
    public function getCustomers(string $storeId);

    /**
     * @param string $storeId
     * @param string $customerId
     *
     * @return array|false
     */
    public function getCustomer(string $storeId, string $customerId);

    /**
     * @param string $storeId
     * @param string $customerId
     * @param array $data
     *
     * @return array|false
     */
    public function updateCustomer(string $storeId, string $customerId, array $data);

    /**
     * @param string $storeId
     * @param string $customerId
     *
     * @return array|false
     */
    public function removeCustomer(string $storeId, string $customerId);

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function addProduct(string $storeId, array $data);

    /**
     * @param string $storeId
     * @param string $productId
     *
     * @return array|false
     */
    public function getProduct(string $storeId, string $productId);

    /**
     * @param string $storeId
     * @param string $productId
     * @param array $data
     *
     * @return array|false
     */
    public function updateProduct(string $storeId, string $productId, array $data);

    /**
     * @param string $storeId
     * @param string $productId
     *
     * @return array|false
     */
    public function removeProduct(string $storeId, string $productId);

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function addOrder(string $storeId, array $data);

    /**
     * @return array|false
     */
    public function getOrders();

    /**
     * @param string $storeId
     *
     * @return array|false
     */
    public function getOrdersByStore(string $storeId);

    /**
     * @param string $storeId
     * @param string $orderId
     *
     * @return array|false
     */
    public function getOrder(string $storeId, string $orderId);

    /**
     * @param string $storeId
     * @param string $orderId
     * @param array $data
     *
     * @return array|false
     */
    public function updateOrder(string $storeId, string $orderId, array $data);

    /**
     * @param string $storeId
     * @param string $orderId
     *
     * @return array|false
     */
    public function removeOrder(string $storeId, string $orderId);

    /**
     * @param string $storeId
     * @param array $data
     *
     * @return array|false
     */
    public function addCart(string $storeId, array $data);

    /**
     * @param string $storeId
     *
     * @return array|false
     */
    public function getCarts(string $storeId);

    /**
     * @param string $storeId
     * @param string $cartId
     *
     * @return array|false
     */
    public function getCart(string $storeId, string $cartId);

    /**
     * @param string $storeId
     * @param string $cartId
     * @param array $data
     *
     * @return array|false
     */
    public function updateCart(string $storeId, string $cartId, array $data);

    /**
     * @param string $storeId
     * @param string $cartId
     *
     * @return array|false
     */
    public function removeCart(string $storeId, string $cartId);
}
