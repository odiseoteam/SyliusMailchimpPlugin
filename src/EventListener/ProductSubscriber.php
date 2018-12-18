<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusMailchimpPlugin\Handler\ProductRegisterHandler;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductSubscriber implements EventSubscriber
{
    /**
     * @var ProductRegisterHandler
     */
    private $productRegisterHandler;

    /**
     * @param ProductRegisterHandler $productRegisterHandler
     */
    public function __construct(ProductRegisterHandler $productRegisterHandler)
    {
        $this->productRegisterHandler = $productRegisterHandler;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
            'postUpdate',
            'postRemove'
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->register($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->unregister($product);
        }
    }

    /**
     * @param ProductInterface $product
     */
    private function register(ProductInterface $product)
    {
        $this->productRegisterHandler->register($product);
    }

    /**
     * @param ProductInterface $product
     */
    private function unregister(ProductInterface $product)
    {
        $this->productRegisterHandler->unregister($product);
    }
}
