<?php

namespace Odiseo\SyliusMailchimpPlugin\Mailchimp;

use Sylius\Component\Core\Model\CustomerInterface;

interface MailchimpInterface
{
    /**
     * @param string $defaultListId
     */
    public function setDefaultListId($defaultListId);

    /**
     * @return string
     */
    public function getDefaultListId();

    /**
     * Add a user to a list.
     *
     * @param CustomerInterface $customer
     * @param string $listId
     */
    public function addUserToList(CustomerInterface $customer, $listId = null);

    public function addStore($data);

    public function getStore($storeId);

    public function updateStore($storeId, $data);

    public function removeStore($storeId);

    public function addCustomer($storeId, $data);

    public function getCustomer($storeId, $customerId);

    public function updateCustomer($storeId, $customerId, $data);

    public function removeCustomer($storeId, $customerId);

    public function addProduct($storeId, $data);

    public function getProduct($storeId, $productId);

    public function updateProduct($storeId, $productId, $data);

    public function removeProduct($storeId, $productId);

    public function addCart($storeId, $data);

    public function getCart($storeId, $cartId);

    public function updateCart($storeId, $cartId, $data);

    public function removeCart($storeId, $cartId);

    public function addOrder($storeId, $data);
}