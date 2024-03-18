<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\ListsInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CustomerNewsletterSubscriptionHandler implements CustomerNewsletterSubscriptionHandlerInterface
{
    public function __construct(
        private ListsInterface $listsApi,
        private EventDispatcherInterface $eventDispatcher,
        private bool $enabled,
    ) {
    }

    public function subscribe(CustomerInterface $customer, string $listId): array
    {
        if (!$this->enabled) {
            return [];
        }

        /** @var string $email */
        $email = $customer->getEmail();

        $subscriberHash = md5(strtolower($email));

        $getMemberResponse = $this->listsApi->getMember($listId, $subscriberHash);
        $isNew = !isset($getMemberResponse['id']);

        $data = [
            'email_address' => $email,
            'status' => 'subscribed',
        ];

        if ($isNew) {
            $event = new GenericEvent($customer, ['data' => $data]);

            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_add');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->listsApi->addMember($listId, $data);
        } else {
            $event = new GenericEvent(
                $customer,
                ['data' => $data, 'existing_mailchimp_member_data' => $getMemberResponse],
            );

            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_update');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->listsApi->updateMember($listId, $subscriberHash, $data);
        }

        return $response;
    }

    public function unsubscribe(CustomerInterface $customer, string $listId): array
    {
        if (!$this->enabled) {
            return [];
        }

        /** @var string $email */
        $email = $customer->getEmail();

        $subscriberHash = md5(strtolower($email));

        $response = $this->listsApi->getMember($listId, $subscriberHash);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($customer, ['listId' => $listId]);

            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_remove');

            return $this->listsApi->removeMember($listId, $subscriberHash);
        }

        return [];
    }
}
