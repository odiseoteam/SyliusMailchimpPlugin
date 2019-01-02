<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandlerInterface;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandlerInterface;
use Odiseo\SyliusMailchimpPlugin\Model\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerSubscriber implements EventSubscriber
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var CustomerRegisterHandlerInterface
     */
    private $customerRegisterHandler;

    /**
     * @var CustomerNewsletterSubscriptionHandlerInterface
     */
    private $customerNewsletterSubscriptionHandler;

    /**
     * @var ListIdProviderInterface
     */
    private $listIdProvider;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     * @param CustomerNewsletterSubscriptionHandlerInterface $customerNewsletterSubscriptionHandler
     * @param ListIdProviderInterface $listIdProvider
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        CustomerNewsletterSubscriptionHandlerInterface $customerNewsletterSubscriptionHandler,
        ListIdProviderInterface $listIdProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->customerNewsletterSubscriptionHandler = $customerNewsletterSubscriptionHandler;
        $this->listIdProvider = $listIdProvider;
    }

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
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->register($customer);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->register($customer);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->unregister($customer);
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    private function register(CustomerInterface $customer)
    {
        $channels = $this->channelRepository->findAll();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $this->customerRegisterHandler->register($customer, $channel);

            if (true === $customer->isSubscribedToNewsletter()) {
                $this->customerNewsletterSubscriptionHandler->subscribe($customer, $this->getListIdByChannel($channel));
            }
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    private function unregister(CustomerInterface $customer)
    {
        $channels = $this->channelRepository->findAll();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $this->customerRegisterHandler->unregister($customer, $channel);
        }
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return string
     */
    private function getListIdByChannel(ChannelInterface $channel): string
    {
        if ($channel instanceof MailchimpListIdAwareInterface) {
            if ($listId = $channel->getListId()) {
                return $listId;
            }
        }

        return $this->listIdProvider->getListId();
    }
}
