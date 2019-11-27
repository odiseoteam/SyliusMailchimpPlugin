<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class StoreSubscriber implements EventSubscriber
{
    /** @var StoreRegisterHandlerInterface */
    private $storeRegisterHandler;

    public function __construct(StoreRegisterHandlerInterface $storeRegisterHandler)
    {
        $this->storeRegisterHandler = $storeRegisterHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
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
    public function postPersist(LifecycleEventArgs $args): void
    {
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->unregister($channel);
        }
    }

    /**
     * @param ChannelInterface $channel
     */
    private function register(ChannelInterface $channel): void
    {
        $this->storeRegisterHandler->register($channel);
    }

    /**
     * @param ChannelInterface $channel
     */
    private function unregister(ChannelInterface $channel): void
    {
        $this->storeRegisterHandler->unregister($channel);
    }
}
