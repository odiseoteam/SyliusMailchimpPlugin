<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerNewsletterSubscriptionHandlerInterface
{
    public function subscribe(CustomerInterface $customer, string $listId): array;

    public function unsubscribe(CustomerInterface $customer, string $listId): array;
}
