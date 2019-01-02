<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Api;

use DrewM\MailChimp\MailChimp;

class Ecommerce extends MailChimp implements EcommerceInterface
{
    /**
     * {@inheritdoc}
     */
    public function addStore(array $data)
    {
        return $this->post('ecommerce/stores', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->get('ecommerce/stores');
    }

    /**
     * {@inheritdoc}
     */
    public function getStore(string $storeId)
    {
        return $this->get('ecommerce/stores/' . $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStore(string $storeId, array $data)
    {
        return $this->patch('ecommerce/stores/' . $storeId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(string $storeId)
    {
        return $this->delete('ecommerce/stores/' . $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomer(string $storeId, array $data)
    {
        return $this->post('ecommerce/stores/' . $storeId . '/customers', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomers(string $storeId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/customers');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(string $storeId, string $customerId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/customers/' . $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomer(string $storeId, string $customerId, array $data)
    {
        return $this->patch('ecommerce/stores/' . $storeId . '/customers/' . $customerId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomer(string $storeId, string $customerId)
    {
        return $this->delete('ecommerce/stores/' . $storeId . '/customers/' . $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function addProduct(string $storeId, array $data)
    {
        return $this->post('ecommerce/stores/' . $storeId . '/products', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts(string $storeId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/products');
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(string $storeId, string $productId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/products/' . $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateProduct(string $storeId, string $productId, array $data)
    {
        return $this->patch('ecommerce/stores/' . $storeId . '/products/' . $productId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProduct(string $storeId, string $productId)
    {
        return $this->delete('ecommerce/stores/' . $storeId . '/products/' . $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder(string $storeId, array $data)
    {
        return $this->post('ecommerce/stores/' . $storeId . '/orders', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders()
    {
        return $this->get('ecommerce/orders');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrdersByStore(string $storeId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/orders');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(string $storeId, string $orderId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/orders/' . $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateOrder(string $storeId, string $orderId, array $data)
    {
        return $this->patch('ecommerce/stores/' . $storeId . '/orders/' . $orderId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeOrder(string $storeId, string $orderId)
    {
        return $this->delete('ecommerce/stores/' . $storeId . '/orders/' . $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function addCart(string $storeId, array $data)
    {
        return $this->post('ecommerce/stores/' . $storeId . '/carts', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCarts(string $storeId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/carts');
    }

    /**
     * {@inheritdoc}
     */
    public function getCart(string $storeId, string $cartId)
    {
        return $this->get('ecommerce/stores/' . $storeId . '/carts/' . $cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCart(string $storeId, string $cartId, array $data)
    {
        return $this->patch('ecommerce/stores/' . $storeId . '/carts/' . $cartId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCart(string $storeId, string $cartId)
    {
        return $this->delete('ecommerce/stores/' . $storeId . '/carts/' . $cartId);
    }
}
