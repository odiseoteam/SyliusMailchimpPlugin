<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandlerInterface;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandlerInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
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
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CustomerRegisterHandlerInterface
     */
    private $customerRegisterHandler;

    /**
     * @var CustomerNewsletterSubscriptionHandlerInterface
     */
    private $customerNewsletterSubscriptionHandler;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param ChannelContextInterface $channelContext
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     * @param CustomerNewsletterSubscriptionHandlerInterface $customerNewsletterSubscriptionHandler
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ChannelContextInterface $channelContext,
        CustomerRegisterHandlerInterface $customerRegisterHandler,
        CustomerNewsletterSubscriptionHandlerInterface $customerNewsletterSubscriptionHandler
    ) {
        $this->channelRepository = $channelRepository;
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->channelContext = $channelContext;
        $this->customerNewsletterSubscriptionHandler = $customerNewsletterSubscriptionHandler;
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
        /** @var ChannelInterface|null $subscribedChannel */
        $subscribedChannel = null;
        try {
            $subscribedChannel = (true === $customer->isSubscribedToNewsletter())?$this->channelContext->getChannel():null;
        } catch (\Exception $e) {
        }

        $channels = $this->channelRepository->findAll();
        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $isSubscribed = !!($subscribedChannel && $subscribedChannel->getCode() == $channel->getCode());
            $this->customerRegisterHandler->register($customer, $channel, $isSubscribed);
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
}
