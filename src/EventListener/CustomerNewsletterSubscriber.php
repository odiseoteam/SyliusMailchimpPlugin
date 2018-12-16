<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\CustomerInterface;

class CustomerNewsletterSubscriber implements EventSubscriber
{
    /**
     * @var MailchimpInterface
     */
    protected $mailchimp;

    /**
     * @param MailchimpInterface $mailchimp
     */
    public function __construct(MailchimpInterface $mailchimp)
    {
        $this->mailchimp = $mailchimp;
    }

    /**
     * @inheritdoc
     */
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
        if ($customer->isSubscribedToNewsletter()) {
            $this->mailchimp->addUserToList($customer);
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
