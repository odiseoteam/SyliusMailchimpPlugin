<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\ListsInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerNewsletterSubscriptionHandler implements CustomerNewsletterSubscriptionHandlerInterface
{
    /** @var ListsInterface */
    private $listsApi;

    /** @var bool */
    private $enabled;

    public function __construct(
        ListsInterface $listsApi,
        bool $enabled
    ) {
        $this->listsApi = $listsApi;
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

        $response = $this->listsApi->getMember($listId, $subscriberHash);
        $isNew = !isset($response['id']);

        $data = [
            'email_address' => $customer->getEmail(),
            'status' => 'subscribed',
        ];

        if ($isNew) {
            $response = $this->listsApi->addMember($listId, $data);
        } else {
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
            return $this->listsApi->removeMember($listId, $subscriberHash);
        }

        return false;
    }
}
