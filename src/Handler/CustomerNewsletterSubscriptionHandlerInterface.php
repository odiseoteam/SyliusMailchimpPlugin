<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerNewsletterSubscriptionHandlerInterface
{
    /**
     * @param CustomerInterface $customer
     * @param string $listId
     */
    public function subscribe(CustomerInterface $customer, string $listId);

    /**
     * @param CustomerInterface $customer
     * @param string $listId
     */
    public function unsubscribe(CustomerInterface $customer, string $listId);
}
