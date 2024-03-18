<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CustomerRegisterHandler implements CustomerRegisterHandlerInterface
{
    public function __construct(
        private EcommerceInterface $ecommerceApi,
        private EventDispatcherInterface $eventDispatcher,
        private bool $enabled,
    ) {
    }

    public function register(
        CustomerInterface $customer,
        ChannelInterface $channel,
        bool $optInStatus = false,
        bool $createOnly = false,
    ): array {
        if (!$this->enabled) {
            return [];
        }

        $customerId = (string) $customer->getId();

        /** @var string $storeId */
        $storeId = $channel->getCode();
        $customerAddress = $this->getCustomerAddress($customer);
        $firstName = $this->getCustomerFirstName($customer, $customerAddress);
        $lastName = $this->getCustomerLastName($customer, $customerAddress);

        $response = $this->ecommerceApi->getCustomer($storeId, $customerId);
        $isNew = !isset($response['id']);

        if (false === $isNew && true === $createOnly) {
            return [];
        }

        $data = [
            'id' => $customerId,
            'email_address' => $customer->getEmail(),
            'opt_in_status' => $optInStatus,
            'first_name' => $firstName ?? '',
            'last_name' => $lastName ?? '',
        ];

        if ($customerAddress instanceof AddressInterface) {
            $data['company'] = $customerAddress->getCompany() ?? '';
            $data['address'] = [
                'address1' => $customerAddress->getStreet() ?? '',
                'city' => $customerAddress->getCity() ?? '',
                'province' => $customerAddress->getProvinceName() ?? '',
                'province_code' => $customerAddress->getProvinceCode() ?? '',
                'postal_code' => $customerAddress->getPostcode() ?? '',
                'country_code' => $customerAddress->getCountryCode() ?? '',
            ];
        }

        $event = new GenericEvent($customer, ['data' => $data, 'channel' => $channel]);
        if ($isNew) {
            $this->eventDispatcher->dispatch($event, 'mailchimp.customer.pre_add');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->addCustomer($storeId, $data);
        } else {
            $this->eventDispatcher->dispatch($event, 'mailchimp.customer.pre_update');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->updateCustomer($storeId, $customerId, $data);
        }

        return $response;
    }

    public function unregister(CustomerInterface $customer, ChannelInterface $channel): array
    {
        if (!$this->enabled) {
            return [];
        }

        $customerId = (string) $customer->getId();

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getCustomer($storeId, $customerId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($customer, ['channel' => $channel]);

            $this->eventDispatcher->dispatch($event, 'mailchimp.customer.pre_remove');

            return $this->ecommerceApi->removeCustomer($storeId, $customerId);
        }

        return [];
    }

    private function getCustomerAddress(CustomerInterface $customer): ?AddressInterface
    {
        $address = $customer->getDefaultAddress();

        if (null === $address && count($customer->getAddresses()) > 0) {
            /** @var AddressInterface $address */
            $address = $customer->getAddresses()->first();
        }

        return $address;
    }

    private function getCustomerFirstName(CustomerInterface $customer, AddressInterface $address = null): ?string
    {
        $firstName = $customer->getFirstName();

        if (null === $firstName && $address instanceof AddressInterface) {
            $firstName = $address->getFirstName();
        }

        return $firstName;
    }

    private function getCustomerLastName(CustomerInterface $customer, AddressInterface $address = null): ?string
    {
        $lastName = $customer->getLastName();

        if (null === $lastName && $address instanceof AddressInterface) {
            $lastName = $address->getLastName();
        }

        return $lastName;
    }
}
