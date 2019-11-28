<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface ProductRegisterHandlerInterface
{
    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     * @param bool $createOnly
     */
    public function register(ProductInterface $product, ChannelInterface $channel, bool $createOnly = false);

    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     */
    public function unregister(ProductInterface $product, ChannelInterface $channel);
}
