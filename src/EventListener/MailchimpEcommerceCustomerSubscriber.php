<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

class MailchimpEcommerceCustomerSubscriber implements EventSubscriber
{
    /**
     *@var MailchimpInterface
     */
    protected $mailchimp;

    /**
     *@var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param MailchimpInterface $mailchimp
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(MailchimpInterface $mailchimp, ChannelContextInterface $channelContext)
    {
        $this->mailchimp = $mailchimp;
        $this->channelContext = $channelContext;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();

        if($customer instanceof CustomerInterface)
        {
            $this->registerCustomer($customer);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();

        if($customer instanceof CustomerInterface)
        {
            $this->registerCustomer($customer);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();

        if($customer instanceof CustomerInterface)
        {
            $this->deleteCustomer($customer);
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    public function registerCustomer(CustomerInterface $customer)
    {
        try{
            $customerId = $customer->getId();

            /** *@var ShopUserInterface $user */
            $user = $customer->getUser();
            if ($user instanceof ChannelAwareInterface) {
                $channel = $user->getChannel();
                if ($channel) {
                    $storeId = $channel->getCode();

                    $response = $this->mailchimp->getCustomer($storeId, $customerId);
                    if (isset($response['id'])) {
                        $data = [
                            'first_name' => $customer->getFirstName()?:'-',
                            'last_name' => $customer->getLastName()?:'-'
                        ];

                        $this->mailchimp->updateCustomer($storeId, $customerId, $data);
                    } else {
                        $data = [
                            'id' => (string)$customerId,
                            'email_address' => $customer->getEmail(),
                            'opt_in_status' => false,
                            'first_name' => $customer->getFirstName()?:'-',
                            'last_name' => $customer->getLastName()?:'-'
                        ];

                        $this->mailchimp->addCustomer($storeId, $data);
                    }
                }
            }
        }catch (\Exception $e) {}
    }

    /**
     * @param CustomerInterface $customer
     */
    public function deleteCustomer(CustomerInterface $customer)
    {
        $customerId = $customer->getId();

        /** *@var ShopUserInterface $user */
        $user = $customer->getUser();
        if ($user instanceof ChannelAwareInterface) {
            $channel = $user->getChannel();

            if ($channel) {
                $storeId = $channel->getCode();

                $this->mailchimp->getCustomer($storeId, $customerId);

                if (isset($response['id'])) {
                    $this->mailchimp->removeCustomer($storeId, $customerId);
                }
            }
        }
    }
}
