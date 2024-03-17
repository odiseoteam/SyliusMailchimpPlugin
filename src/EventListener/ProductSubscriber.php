<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\ProductRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductSubscriber implements EventSubscriber
{
    public function __construct(
        private ProductRegisterHandlerInterface $productRegisterHandler
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
        $product = $args->getObject();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $product = $args->getObject();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $product = $args->getObject();

        if ($product instanceof ProductInterface) {
            $this->unregister($product);
        }
    }

    private function register(ProductInterface $product): void
    {
        /** @var ChannelInterface $channel */
        foreach ($product->getChannels() as $channel) {
            $this->productRegisterHandler->register($product, $channel);
        }
    }

    private function unregister(ProductInterface $product): void
    {
        /** @var ChannelInterface $channel */
        foreach ($product->getChannels() as $channel) {
            $this->productRegisterHandler->unregister($product, $channel);
        }
    }
}
