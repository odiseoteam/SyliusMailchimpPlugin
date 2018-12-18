<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandler;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandler;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerListener
{
    /**
     * @var CustomerRegisterHandler
     */
    private $customerRegisterHandler;

    /**
     * @var CustomerNewsletterSubscriptionHandler
     */
    private $customerNewsletterSubscriptionHandler;

    /**
     * @param CustomerRegisterHandler $customerRegisterHandler
     * @param CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler
     */
    public function __construct(
        CustomerRegisterHandler $customerRegisterHandler,
        CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler
    )
    {
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->customerNewsletterSubscriptionHandler = $customerNewsletterSubscriptionHandler;
    }

    /**
     * @param GenericEvent $event
     */
    public function customerCreate(GenericEvent $event)
    {
        /** @var CustomerInterface $customer */
        $customer = $event->getSubject();

        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException(
                $customer,
                CustomerInterface::class
            );
        }

        $this->register($customer);

        $customer->isSubscribedToNewsletter() === false ? $this->unsubscribe($customer) : $this->subscribe($customer);
    }

    /**
     * @param GenericEvent $event
     */
    public function customerUpdate(GenericEvent $event)
    {
        $this->customerCreate($event);
    }

    private function register(CustomerInterface $customer)
    {
        $this->customerRegisterHandler->register($customer);
    }

    private function subscribe(CustomerInterface $customer)
    {
        $this->customerNewsletterSubscriptionHandler->subscribe($customer->getEmail());
    }

    private function unsubscribe(CustomerInterface $customer)
    {
        $this->customerNewsletterSubscriptionHandler->unsubscribe($customer);
    }
}
