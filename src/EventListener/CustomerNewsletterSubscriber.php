<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\CustomerInterface;

class CustomerNewsletterSubscriber implements EventSubscriber
{
    /**
     * @var MailchimpService
     */
    protected $mailchimpService;

    /**
     * @param MailchimpService $mailchimpService
     */
    public function __construct(MailchimpService $mailchimpService)
    {
        $this->mailchimpService = $mailchimpService;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    /**
     * @param CustomerInterface $customer
     */
    public function addCustomerToList(CustomerInterface $customer)
    {
        if ($customer->isSubscribedToNewsletter())
        {
            $this->mailchimpService->addUserToList(null, $customer->getEmail(), $customer->getFirstName(), $customer->getLastName());
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $this->manageCustomerNewsletter($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->manageCustomerNewsletter($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    protected function manageCustomerNewsletter(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof CustomerInterface) {
            return;
        }

        $this->addCustomerToList($entity);
    }
}
