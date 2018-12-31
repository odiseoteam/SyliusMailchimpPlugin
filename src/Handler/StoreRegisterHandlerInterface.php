<?php

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;

interface StoreRegisterHandlerInterface
{
    /**
     * @param ChannelInterface $channel
     * @param bool $isSyncing
     *
     * @return array|false
     */
    public function register(ChannelInterface $channel, bool $isSyncing = false);

    /**
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function unregister(ChannelInterface $channel);
}
