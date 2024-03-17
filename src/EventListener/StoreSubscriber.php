<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class StoreSubscriber implements EventSubscriber
{
    public function __construct(
        private StoreRegisterHandlerInterface $storeRegisterHandler
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            'postPersist',
            'postUpdate',
            'postRemove',
        ];
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $channel = $args->getObject();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $channel = $args->getObject();

        if ($channel instanceof ChannelInterface) {
            $this->register($channel);
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $channel = $args->getObject();

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
