<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerRegisterHandlerInterface
{
    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface $channel
     * @param bool $optInStatus
     * @param bool $createOnly
     */
    public function register(
        CustomerInterface $customer,
        ChannelInterface $channel,
        bool $optInStatus = false,
        bool $createOnly = false
    );

    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface $channel
     */
    public function unregister(CustomerInterface $customer, ChannelInterface $channel);
}
