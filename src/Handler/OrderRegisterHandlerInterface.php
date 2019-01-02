<?php

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderRegisterHandlerInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return array|null
     */
    public function register(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @return array|null
     */
    public function unregister(OrderInterface $order);
}