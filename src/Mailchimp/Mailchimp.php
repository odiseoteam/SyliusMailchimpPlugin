<?php

namespace Odiseo\SyliusMailchimpPlugin\Mailchimp;

use DrewM\MailChimp\MailChimp as BaseMailChimp;
use Sylius\Component\Core\Model\CustomerInterface;

class Mailchimp extends BaseMailChimp implements MailchimpInterface
{
    /**
     * @var string
     */
    protected $defaultListId;

    /**
     * @inheritdoc
     */
    public function setDefaultListId($defaultListId)
    {
        $this->defaultListId = $defaultListId;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultListId()
    {
        return $this->defaultListId;
    }

    /**
     * @inheritdoc
     */
    public function addUserToList(CustomerInterface $customer, $listId = null)
    {
        $listId = $listId?$listId:$this->defaultListId;

        try {
            $this->post("lists/".$listId."/members", [
                'merge_fields' => ['FNAME'=> $customer->getFirstName(), 'LNAME'=> $customer->getLastName()],
                'email_address' => $customer->getEmail(),
                'status'        => 'subscribed',
            ]);
        }catch (\Exception $e)
        {}
    }

    /**
     * @inheritdoc
     */
    public function addStore($data)
    {
        return $this->post('ecommerce/stores', $data);
    }

    /**
     * @inheritdoc
     */
    public function getStore($storeId)
    {
        return $this->get('ecommerce/stores/'.$storeId);
    }

    /**
     * @inheritdoc
     */
    public function updateStore($storeId, $data)
    {
        return $this->patch('ecommerce/stores/'.$storeId, $data);
    }

    /**
     * @inheritdoc
     */
    public function removeStore($storeId)
    {
        return $this->delete('ecommerce/stores/'.$storeId);
    }

    /**
     * @inheritdoc
     */
    public function addCustomer($storeId, $data)
    {
        return $this->post('ecommerce/stores/'.$storeId.'/customers', $data);
    }

    /**
     * @inheritdoc
     */
    public function getCustomer($storeId, $customerId)
    {
        return $this->get('ecommerce/stores/'.$storeId.'/customers/'.$customerId);
    }

    /**
     * @inheritdoc
     */
    public function updateCustomer($storeId, $customerId, $data)
    {
        return $this->patch('ecommerce/stores/'.$storeId.'/customers/'.$customerId, $data);
    }

    /**
     * @inheritdoc
     */
    public function removeCustomer($storeId, $customerId)
    {
        return $this->delete('ecommerce/stores/'.$storeId.'/customers/'.$customerId);
    }

    /**
     * @inheritdoc
     */
    public function addProduct($storeId, $data)
    {
        return $this->post('ecommerce/stores/'.$storeId.'/products', $data);
    }

    /**
     * @inheritdoc
     */
    public function getProduct($storeId, $productId)
    {
        return $this->get('ecommerce/stores/'.$storeId.'/products/'.$productId);
    }

    /**
     * @inheritdoc
     */
    public function updateProduct($storeId, $productId, $data)
    {
        return $this->patch('ecommerce/stores/'.$storeId.'/products/'.$productId, $data);
    }

    /**
     * @inheritdoc
     */
    public function removeProduct($storeId, $productId)
    {
        return $this->delete('ecommerce/stores/'.$storeId.'/products/'.$productId);
    }

    /**
     * @inheritdoc
     */
    public function addCart($storeId, $data)
    {
        return $this->post('ecommerce/stores/'.$storeId.'/carts', $data);
    }

    /**
     * @inheritdoc
     */
    public function getCart($storeId, $cartId)
    {
        return $this->get('ecommerce/stores/'.$storeId.'/carts/'.$cartId);
    }

    /**
     * @inheritdoc
     */
    public function updateCart($storeId, $cartId, $data)
    {
        return $this->patch('ecommerce/stores/'.$storeId.'/carts/'.$cartId, $data);
    }

    /**
     * @inheritdoc
     */
    public function removeCart($storeId, $cartId)
    {
        return $this->delete('ecommerce/stores/'.$storeId.'/carts/'.$cartId);
    }

    /**
     * @inheritdoc
     */
    public function addOrder($storeId, $data)
    {
        return $this->post('ecommerce/stores/'.$storeId.'/orders', $data);
    }
}