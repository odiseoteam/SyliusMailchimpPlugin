<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderRegisterHandlerInterface
{
    /**
     * @param OrderInterface $order
     * @param bool $createOnly
     *
     * @return array|false
     */
    public function register(OrderInterface $order, bool $createOnly = false);

    /**
     * @param OrderInterface $order
     *
     * @return array|false
     */
    public function unregister(OrderInterface $order);
}
