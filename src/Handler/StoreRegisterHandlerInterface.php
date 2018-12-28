<?php

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Odiseo\SyliusMailchimpPlugin\Model\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

interface StoreRegisterHandlerInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function register(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     *
     * @return array|false
     */
    public function unregister(ChannelInterface $channel);
}
