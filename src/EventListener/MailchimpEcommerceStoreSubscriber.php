<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ChannelInterface;

class MailchimpEcommerceStoreSubscriber implements EventSubscriber
{
    /**
     *@var MailchimpService
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
            'postRemove'
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->registerStore($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->registerStore($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->deleteStore($args);
    }

    public function registerStore(LifecycleEventArgs $args)
    {
        try{
            $channel = $args->getEntity();

            if ($channel instanceof ChannelInterface) {
                $storeId = $channel->getCode();

                $response = $this->mailchimpService->getStore($storeId);
                if (isset($response['id'])) {
                    $data = [
                        'name' => $channel->getName(),
                        'currency_code' => $channel->getBaseCurrency()->getCode()
                    ];

                    $this->mailchimpService->updateStore($storeId, $data);
                } else {
                    $listId = $channel->getListId();
                    if ($listId) {
                        $data = [
                            'id' => $storeId,
                            'list_id' => $listId,
                            'name' => $channel->getName(),
                            'currency_code' => $channel->getBaseCurrency()->getCode()
                        ];

                        $this->mailchimpService->addStore($data);
                    }
                }
            }
        }catch (\Exception $e) {
        }
    }

    public function deleteStore(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if ($entity instanceof ChannelInterface) {
            $storeId = $entity->getCode();

            $response = $this->mailchimpService->getStore($storeId);
            if (isset($response['id'])) {
                $this->mailchimpService->removeStore($storeId);
            }
        }
    }
}
