<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class CustomerRegisterHandler
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
     */
    public function register(CustomerInterface $customer)
    {
        $customerId = (string)$customer->getId();

        /** *@var ShopUserInterface $user */
        $user = $customer->getUser();
        if ($user instanceof ChannelAwareInterface) {
            $channel = $user->getChannel();
            if ($channel) {
                $storeId = $channel->getCode();

                $response = $this->ecommerceApi->getCustomer($storeId, $customerId);

                Assert::keyExists($response, 'status');

                if ($response['status'] === Response::HTTP_NOT_FOUND) {
                    $data = [
                        'id' => $customerId,
                        'email_address' => $customer->getEmail(),
                        'opt_in_status' => false,
                        'first_name' => $customer->getFirstName() ? $customer->getFirstName() : '-',
                        'last_name' => $customer->getLastName() ? $customer->getLastName() : '-'
                    ];

                    $this->ecommerceApi->addCustomer($storeId, $data);
                } else {
                    $data = [
                        'first_name' => $customer->getFirstName() ? $customer->getFirstName() : '-',
                        'last_name' => $customer->getLastName() ? $customer->getLastName() : '-'
                    ];

                    $this->ecommerceApi->updateCustomer($storeId, $customerId, $data);
                }
            }
        }
    }
}
