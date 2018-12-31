<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerRegisterHandler implements CustomerRegisterHandlerInterface
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @param EcommerceInterface $ecommerceApi
     */
    public function __construct(
        EcommerceInterface $ecommerceApi
    ) {
        $this->ecommerceApi = $ecommerceApi;
    }

    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function register(CustomerInterface $customer, ChannelInterface $channel)
    {
        $customerId = (string) $customer->getId();
        $storeId = $channel->getCode();
        $customerAddress = $this->getCustomerAddress($customer);

        $response = $this->ecommerceApi->getCustomer($storeId, $customerId);
        $isNew = !isset($response['id']);

        $firstName = $this->getCustomerFirstName($customer, $customerAddress);
        $lastName = $this->getCustomerLastName($customer, $customerAddress);

        $data = [
            'id' => $customerId,
            'email_address' => $customer->getEmail(),
            'opt_in_status' => true,
            'first_name' => $firstName?:'',
            'last_name' => $lastName?:'',
        ];

        if ($customerAddress) {
            $data['company'] = $customerAddress->getCompany()?:'';
            $data['address'] = [
                'address1' => $customerAddress->getStreet()?:'',
                'city' => $customerAddress->getCity()?:'',
                'province' => $customerAddress->getProvinceName()?:'',
                'province_code' => $customerAddress->getProvinceCode()?:'',
                'postal_code' => $customerAddress->getPostcode()?:'',
                'country_code' => $customerAddress->getCountryCode()?:'',
            ];
        }

        if ($isNew) {
            $response = $this->ecommerceApi->addCustomer($storeId, $data);
        } else {
            $response = $this->ecommerceApi->updateCustomer($storeId, $customerId, $data);
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function unregister(CustomerInterface $customer, ChannelInterface $channel)
    {
        $customerId = (string) $customer->getId();
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getCustomer($storeId, $customerId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeCustomer($storeId, $customerId);
        }

        return false;
    }

    /**
     * @param CustomerInterface $customer
     * @return null|AddressInterface
     */
    private function getCustomerAddress(CustomerInterface $customer): ?AddressInterface
    {
        $address = $customer->getDefaultAddress();

        if (!$address && count($customer->getAddresses()) > 0) {
            $address = $customer->getAddresses()->first();
        }

        return $address;
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface|null $address
     *
     * @return null|string
     */
    protected function getCustomerFirstName(CustomerInterface $customer, AddressInterface $address = null): ?string
    {
        $firstName = $customer->getFirstName();

        if (!$firstName && $address) {
            $firstName = $address->getFirstName();
        }

        return $firstName;
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface|null $address
     *
     * @return null|string
     */
    protected function getCustomerLastName(CustomerInterface $customer, AddressInterface $address = null): ?string
    {
        $lastName = $customer->getLastName();

        if (!$lastName && $address) {
            $lastName = $address->getLastName();
        }

        return $lastName;
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface|null $address
     *
     * @return null|string
     */
    protected function getCustomerPhoneNumber(CustomerInterface $customer, AddressInterface $address = null): ?string
    {
        $phoneNumber = $customer->getPhoneNumber();

        if (!$phoneNumber && $address) {
            $phoneNumber = $address->getPhoneNumber();
        }

        return $phoneNumber;
    }
}
