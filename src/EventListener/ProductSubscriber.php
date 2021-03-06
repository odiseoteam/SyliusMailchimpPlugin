<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\ProductRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductSubscriber implements EventSubscriber
{
    /** @var ProductRegisterHandlerInterface */
    private $productRegisterHandler;

    public function __construct(ProductRegisterHandlerInterface $productRegisterHandler)
    {
        $this->productRegisterHandler = $productRegisterHandler;
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
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->unregister($product);
        }
    }

    /**
     * @param ProductInterface $product
     */
    private function register(ProductInterface $product): void
    {
        /** @var ChannelInterface $channel */
        foreach ($product->getChannels() as $channel) {
            $this->productRegisterHandler->register($product, $channel);
        }
    }

    /**
     * @param ProductInterface $product
     */
    private function unregister(ProductInterface $product): void
    {
        /** @var ChannelInterface $channel */
        foreach ($product->getChannels() as $channel) {
            $this->productRegisterHandler->unregister($product, $channel);
        }
    }
}
