<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerRegisterHandlerInterface
{
    public function register(
        CustomerInterface $customer,
        ChannelInterface $channel,
        bool $optInStatus = false,
        bool $createOnly = false,
    ): array;

    public function unregister(CustomerInterface $customer, ChannelInterface $channel): array;
}
