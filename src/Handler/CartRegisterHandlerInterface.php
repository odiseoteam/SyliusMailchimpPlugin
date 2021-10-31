<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;

interface CartRegisterHandlerInterface
{
    public function register(OrderInterface $order, bool $createOnly = false): array;

    public function unregister(OrderInterface $order): array;
}
