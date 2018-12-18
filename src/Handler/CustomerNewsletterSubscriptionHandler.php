<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusMailchimpPlugin\Api\ListsInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

class CustomerNewsletterSubscriptionHandler
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var EntityManagerInterface
     */
    private $customerManager;

    /**
     * @var ListsInterface
     */
    private $listsApi;

    /**
     * @var string
     */
    private $listId;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param FactoryInterface $customerFactory
     * @param EntityManagerInterface $customerManager
     * @param ListsInterface $listsApi
     * @param string $listId
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        EntityManagerInterface $customerManager,
        ListsInterface $listsApi,
        string $listId
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerManager = $customerManager;
        $this->listsApi = $listsApi;
        $this->listId = $listId;
    }

    /**
     * @param string $email
     */
    public function subscribe($email)
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        if ($customer instanceof CustomerInterface) {
            $this->updateCustomer($customer);
        } else {
            $customer = $this->createCustomer($email);
        }

        $response = $this->listsApi->getMember($this->listId, md5(strtolower($email)));
        Assert::keyExists($response, 'status');

        if ($response['status'] === Response::HTTP_NOT_FOUND) {
            $data = [
                'merge_fields' => [
                    'FNAME'=> $customer->getFirstName() ? $customer->getFirstName() : '-',
                    'LNAME'=> $customer->getLastName() ? $customer->getLastName() : '-'
                ],
                'email_address' => $customer->getEmail(),
                'status' => 'subscribed'
            ];

            $response = $this->listsApi->addMember($this->listId, $data);

            Assert::keyExists($response, 'status');

            if ($response['status'] !== 'subscribed') {
                throw new BadRequestHttpException();
            }
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    public function unsubscribe(CustomerInterface $customer)
    {
        $this->updateCustomer($customer, false);

        $email = $customer->getEmail();

        $this->listsApi->removeMember($this->listId, md5(strtolower($email)));
    }

    /**
     * @param $email
     * @return CustomerInterface
     */
    private function createCustomer($email)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setEmail($email);
        $customer->setSubscribedToNewsletter(true);

        $this->customerRepository->add($customer);

        return $customer;
    }

    /**
     * @param CustomerInterface $customer
     * @param bool $subscribedToNewsletter
     */
    private function updateCustomer(CustomerInterface $customer, $subscribedToNewsletter = true)
    {
        $customer->setSubscribedToNewsletter($subscribedToNewsletter);
        $this->customerManager->flush();
    }
}
