<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ChannelInterface;

class MailchimpEcommerceStoreSubscriber implements EventSubscriber
{
    /**
     *@var MailchimpInterface
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
        return [
            'postPersist',
            'postUpdate',
            'postRemove',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $channel = $args->getEntity();

        if($channel instanceof ChannelInterface)
        {
            $this->registerStore($channel);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $channel = $args->getEntity();

        if($channel instanceof ChannelInterface)
        {
            $this->registerStore($channel);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $channel = $args->getEntity();

        if($channel instanceof ChannelInterface)
        {
            $this->deleteStore($channel);
        }
    }

    /**
     * @param ChannelInterface $channel
     */
    public function registerStore(ChannelInterface $channel)
    {
        try{
            $storeId = $channel->getCode();

            $response = $this->mailchimp->getStore($storeId);

            if (isset($response['id'])) {
                $data = [
                    'name' => $channel->getName(),
                    'currency_code' => $channel->getBaseCurrency()->getCode()
                ];

                $this->mailchimp->updateStore($storeId, $data);
            } else {
                $listId = $this->mailchimp->getDefaultListId();

                if ($listId) {
                    $data = [
                        'id' => $storeId,
                        'list_id' => $listId,
                        'name' => $channel->getName(),
                        'currency_code' => $channel->getBaseCurrency()->getCode()
                    ];

                    $this->mailchimp->addStore($data);
                }
            }
        }catch (\Exception $e) {}
    }

    /**
     * @param ChannelInterface $channel
     */
    public function deleteStore(ChannelInterface $channel)
    {
        $storeId = $channel->getCode();

        $response = $this->mailchimp->getStore($storeId);
        if (isset($response['id'])) {
            $this->mailchimp->removeStore($storeId);
        }
    }
}
