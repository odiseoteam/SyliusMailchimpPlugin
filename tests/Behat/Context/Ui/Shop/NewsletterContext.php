<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusMailchimpPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorage;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Tests\Odiseo\SyliusMailchimpPlugin\Behat\Page\Shop\Newsletter\NewsletterPageInterface;
use Webmozart\Assert\Assert;

final class NewsletterContext implements Context
{
    /**
     * @var NewsletterPageInterface
     */
    private $newsletterPage;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var EntityManagerInterface
     */
    private $customerManager;

    /**
     * @var SharedStorage
     */
    private $sharedStorage;

    /**
     * @param NewsletterPageInterface $newsletterPage
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param EntityManagerInterface $customerManager
     * @param SharedStorage $sharedStorage
     */
    public function __construct(
        NewsletterPageInterface $newsletterPage,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        EntityManagerInterface $customerManager,
        SharedStorage $sharedStorage
    )
    {
        $this->newsletterPage = $newsletterPage;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerManager = $customerManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I want to subscribe to the newsletter
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToSubscribeToTheNewsletter()
    {
        $this->newsletterPage->open();
    }

    /**
     * @When I fill newsletter with :email email
     * @param $email
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillNewsletterWithEmail($email)
    {
        $this->newsletterPage->fillEmail($email);
    }

    /**
     * @Then I should be notified that I am subscribed to the newsletter
     */
    public function iShouldBeNotifiedThatIAmSubscribedToTheNewsletter()
    {
        $contents = json_decode($this->newsletterPage->getContents());
        Assert::contains($contents->message, "Subscribed succesfully");
    }

    /**
     * @When I subscribe to it
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iSubscribeToIt()
    {
        $this->newsletterPage->subscribe();
    }
}
