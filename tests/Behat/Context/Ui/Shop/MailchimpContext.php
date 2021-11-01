<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Odiseo\SyliusMailchimpPlugin\Api\ListsInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Odiseo\SyliusMailchimpPlugin\Behat\Fake\MailchimpApi;
use Webmozart\Assert\Assert;

final class MailchimpContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var MailchimpApi */
    private $mailchimpApi;

    /** @var string */
    private $listId;

    /** @var string */
    private $subscribedEmail;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        string $api_key,
        ListIdProviderInterface $listIdProvider
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->mailchimpApi = new MailchimpApi($api_key);
        $this->listId = $listIdProvider->getListId();
    }

    /**
     * @Given there is a created list in MailChimp with specified ID
     */
    public function thereIsAMailChimpListWithSpecifiedId(): void
    {
        Assert::notNull($this->listId);
    }

    /**
     * @Then the email :email should be exported to MailChimp's default list
     * @param string $email
     */
    public function theEmailShouldBeExportedToMailChimp(string $email): void
    {
        $emailHash = $this->getSubscriberHash($email);

        $response = $this->mailchimpApi->get('lists/' . $this->listId. '/members/' . $emailHash);

        Assert::keyExists($response, 'status');
        Assert::eq($response['status'], 'subscribed', sprintf(
            "The email %s doesn't exist in MailChimp with list with %s ID",
            $email,
            $this->listId
        ));

        $this->subscribedEmail = $email;
    }

    /**
     * @param string $email
     * @return string
     */
    private function getSubscriberHash(string $email): string
    {
        return md5(strtolower($email));
    }
}
