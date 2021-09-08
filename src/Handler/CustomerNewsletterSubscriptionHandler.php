<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\ListsInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CustomerNewsletterSubscriptionHandler implements CustomerNewsletterSubscriptionHandlerInterface
{
    /** @var ListsInterface */
    private $listsApi;

    /** @var bool */
    private $enabled;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        ListsInterface $listsApi,
        EventDispatcherInterface $eventDispatcher,
        bool $enabled
    ) {
        $this->listsApi = $listsApi;
        $this->eventDispatcher = $eventDispatcher;
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(CustomerInterface $customer, string $listId)
    {
        if (!$this->enabled) {
            return false;
        }

        $subscriberHash = md5(strtolower($customer->getEmail()));

        $getMemberResponse = $this->listsApi->getMember($listId, $subscriberHash);
        $isNew = !isset($getMemberResponse['id']);

        $data = [
            'email_address' => $customer->getEmail(),
            'status' => 'subscribed',
        ];

        if ($isNew) {
            $event = new GenericEvent($customer, ['data' => $data]);
            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_add');
            $data = $event->getArgument('data');

            $response = $this->listsApi->addMember($listId, $data);
        } else {
            $event = new GenericEvent($customer, ['data' => $data, 'existing_mailchimp_member_data' => $getMemberResponse]);
            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_update');
            $data = $event->getArgument('data');

            $response = $this->listsApi->updateMember($listId, $subscriberHash, $data);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(CustomerInterface $customer, string $listId)
    {
        if (!$this->enabled) {
            return false;
        }

        $subscriberHash = md5(strtolower($customer->getEmail()));

        $response = $this->listsApi->getMember($listId, $subscriberHash);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($customer, ['listId' => $listId]);
            $this->eventDispatcher->dispatch($event, 'mailchimp.customer_newsletter.pre_remove');

            return $this->listsApi->removeMember($listId, $subscriberHash);
        }

        return false;
    }
}
