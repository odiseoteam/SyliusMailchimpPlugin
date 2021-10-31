<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface ProductRegisterHandlerInterface
{
    public function register(ProductInterface $product, ChannelInterface $channel, bool $createOnly = false): array;

    public function unregister(ProductInterface $product, ChannelInterface $channel): array;
}
