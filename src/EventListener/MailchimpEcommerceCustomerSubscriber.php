<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Core\Model\Customer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ShopUserInterface;

class MailchimpEcommerceCustomerSubscriber implements EventSubscriber
{
    /**
     *@var MailchimpService
     */
    protected $mailchimpService;

    /**
     *@var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param MailchimpService $mailchimpService
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(MailchimpService $mailchimpService, ChannelContextInterface $channelContext)
    {
        $this->mailchimpService = $mailchimpService;
        $this->channelContext = $channelContext;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->registerCustomer($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->registerCustomer($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->deleteCustomer($args);
    }

    public function registerCustomer(LifecycleEventArgs $args)
    {
        try{
            $customer = $args->getEntity();

            if ($customer instanceof Customer) {
                $customerId = $customer->getId();

                /** *@var ShopUserInterface $user */
                $user = $customer->getUser();
                if ($user instanceof ChannelAwareInterface) {
                    $channel = $user->getChannel();
                    if ($channel) {
                        $storeId = $channel->getCode();

                        $response = $this->mailchimpService->getCustomer($storeId, $customerId);
                        if (isset($response['id'])) {
                            $data = [
                                'first_name' => $customer->getFirstName()?:'-',
                                'last_name' => $customer->getLastName()?:'-'
                            ];

                            $this->mailchimpService->updateCustomer($storeId, $customerId, $data);
                        } else {
                            $data = [
                                'id' => (string)$customerId,
                                'email_address' => $customer->getEmail(),
                                'opt_in_status' => false,
                                'first_name' => $customer->getFirstName()?:'-',
                                'last_name' => $customer->getLastName()?:'-'
                            ];

                            $this->mailchimpService->addCustomer($storeId, $data);
                        }
                    }
                }
            }
        }catch (\Exception $e) {
        }
    }

    public function deleteCustomer(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();
        
        if ($customer instanceof Customer) {
            $customerId = $customer->getId();

            /** *@var ShopUserInterface $user */
            $user = $customer->getUser();
            if ($user instanceof ChannelAwareInterface) {
                $channel = $user->getChannel();
                if ($channel) {
                    $storeId = $channel->getCode();

                    $this->mailchimpService->getCustomer($storeId, $customerId);
                    if (isset($response['id'])) {
                        $this->mailchimpService->removeCustomer($storeId, $customerId);
                    }
                }
            }
        }
    }
}
