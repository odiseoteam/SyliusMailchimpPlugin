<?php

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerRegisterHandlerInterface
{
    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function register(CustomerInterface $customer, ChannelInterface $channel);

    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function unregister(CustomerInterface $customer, ChannelInterface $channel);
}