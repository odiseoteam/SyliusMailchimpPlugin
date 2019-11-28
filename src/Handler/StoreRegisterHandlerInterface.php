<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;

interface StoreRegisterHandlerInterface
{
    /**
     * @param ChannelInterface $channel
     * @param bool $isSyncing
     */
    public function register(ChannelInterface $channel, bool $isSyncing = false);

    /**
     * @param ChannelInterface $channel
     */
    public function unregister(ChannelInterface $channel);
}
