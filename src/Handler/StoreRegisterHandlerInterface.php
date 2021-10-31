<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;

interface StoreRegisterHandlerInterface
{
    public function register(ChannelInterface $channel, bool $isSyncing = false): array;

    public function unregister(ChannelInterface $channel): array;
}
