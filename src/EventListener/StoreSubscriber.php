<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class StoreSubscriber implements EventSubscriber
{
    private StoreRegisterHandlerInterface $storeRegisterHandler;

    public function __construct(StoreRegisterHandlerInterface $storeRegisterHandler)
    {
        $this->storeRegisterHandler = $storeRegisterHandler;
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
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $channel = $args->getEntity();

        if ($channel instanceof ChannelInterface) {
            $this->unregister($channel);
        }
    }

    private function register(ChannelInterface $channel): void
    {
        $this->storeRegisterHandler->register($channel);
    }

    private function unregister(ChannelInterface $channel): void
    {
        $this->storeRegisterHandler->unregister($channel);
    }
}
