<?php

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;

interface CartRegisterHandlerInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return array|false
     */
    public function register(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @return array|false
     */
    public function unregister(OrderInterface $order);
}