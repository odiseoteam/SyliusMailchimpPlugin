<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;

interface CartRegisterHandlerInterface
{
    /**
     * @param OrderInterface $order
     * @param bool $createOnly
     */
    public function register(OrderInterface $order, bool $createOnly = false);

    /**
     * @param OrderInterface $order
     */
    public function unregister(OrderInterface $order);
}
