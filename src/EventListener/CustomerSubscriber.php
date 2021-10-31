<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerSubscriber implements EventSubscriber
{
    private ChannelRepositoryInterface $channelRepository;
    private ChannelContextInterface $channelContext;
    private CustomerRegisterHandlerInterface $customerRegisterHandler;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ChannelContextInterface $channelContext,
        CustomerRegisterHandlerInterface $customerRegisterHandler
    ) {
        $this->channelRepository = $channelRepository;
        $this->customerRegisterHandler = $customerRegisterHandler;
        $this->channelContext = $channelContext;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
            'postUpdate',
            'postRemove',
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->register($customer);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->register($customer);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $customer = $args->getEntity();

        if ($customer instanceof CustomerInterface) {
            $this->unregister($customer);
        }
    }

    private function register(CustomerInterface $customer): void
    {
        /** @var ChannelInterface|null $subscribedChannel */
        $subscribedChannel = null;
        try {
            $subscribedChannel = $customer->isSubscribedToNewsletter() ? $this->channelContext->getChannel() : null;
        } catch (\Exception $e) {
        }

        $channels = $this->channelRepository->findAll();
        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $isSubscribed = !!(null !== $subscribedChannel && $subscribedChannel->getCode() == $channel->getCode());
            $this->customerRegisterHandler->register($customer, $channel, $isSubscribed);
        }
    }

    private function unregister(CustomerInterface $customer): void
    {
        $channels = $this->channelRepository->findAll();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $this->customerRegisterHandler->unregister($customer, $channel);
        }
    }
}
