<?php

namespace Odiseo\SyliusMailchimpPlugin\Service;

use DrewM\MailChimp\MailChimp;

class MailchimpService
{
    /** @var MailChimp */
    protected $mailchimp;

    /** @var string */
    protected $apiKey;

    /**
     * MailchimpService constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->mailchimp = new MailChimp($this->apiKey);
    }

    /**
     * Add a user to a list.
     *
     * @param $listId
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    public function addUserToList($listId, $email, $firstName = null, $lastName = null)
    {
        if($listId)
        {
            try{
                $this->mailchimp->post("lists/".$listId."/members", [
                    'merge_fields' => ['FNAME'=> $firstName, 'LNAME'=> $lastName],
                    'email_address' => $email,
                    'status'        => 'subscribed',
                ]);
            }catch (\Exception $e)
            {
            }
        }
    }

    public function addStore($data)
    {
        return $this->mailchimp->post('ecommerce/stores', $data);
    }

    public function getStore($storeId)
    {
        return $this->mailchimp->get('ecommerce/stores/'.$storeId);
    }

    public function updateStore($storeId, $data)
    {
        return $this->mailchimp->patch('ecommerce/stores/'.$storeId, $data);
    }

    public function removeStore($storeId)
    {
        return $this->mailchimp->delete('ecommerce/stores/'.$storeId);
    }

    public function addCustomer($storeId, $data)
    {
        return $this->mailchimp->post('ecommerce/stores/'.$storeId.'/customers', $data);
    }

    public function getCustomer($storeId, $customerId)
    {
        return $this->mailchimp->get('ecommerce/stores/'.$storeId.'/customers/'.$customerId);
    }

    public function updateCustomer($storeId, $customerId, $data)
    {
        return $this->mailchimp->patch('ecommerce/stores/'.$storeId.'/customers/'.$customerId, $data);
    }

    public function removeCustomer($storeId, $customerId)
    {
        return $this->mailchimp->delete('ecommerce/stores/'.$storeId.'/customers/'.$customerId);
    }

    public function addProduct($storeId, $data)
    {
        return $this->mailchimp->post('ecommerce/stores/'.$storeId.'/products', $data);
    }

    public function getProduct($storeId, $productId)
    {
        return $this->mailchimp->get('ecommerce/stores/'.$storeId.'/products/'.$productId);
    }

    public function updateProduct($storeId, $productId, $data)
    {
        return $this->mailchimp->patch('ecommerce/stores/'.$storeId.'/products/'.$productId, $data);
    }

    public function removeProduct($storeId, $productId)
    {
        return $this->mailchimp->delete('ecommerce/stores/'.$storeId.'/products/'.$productId);
    }

    public function addCart($storeId, $data)
    {
        return $this->mailchimp->post('ecommerce/stores/'.$storeId.'/carts', $data);
    }

    public function getCart($storeId, $cartId)
    {
        return $this->mailchimp->get('ecommerce/stores/'.$storeId.'/carts/'.$cartId);
    }

    public function updateCart($storeId, $cartId, $data)
    {
        return $this->mailchimp->patch('ecommerce/stores/'.$storeId.'/carts/'.$cartId, $data);
    }

    public function removeCart($storeId, $cartId)
    {
        return $this->mailchimp->delete('ecommerce/stores/'.$storeId.'/carts/'.$cartId);
    }

    public function addOrder($storeId, $data)
    {
        return $this->mailchimp->post('ecommerce/stores/'.$storeId.'/orders', $data);
    }
}