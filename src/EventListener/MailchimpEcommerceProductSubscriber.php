<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;

class MailchimpEcommerceProductSubscriber implements EventSubscriber
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
        $this->registerProduct($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->registerProduct($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->deleteProduct($args);
    }

    public function registerProduct(LifecycleEventArgs $args)
    {
        try {
            $product = $args->getEntity();

            if ($product instanceof ProductInterface) {
                $productId = $product->getId();

                $variants = array();
                $productVariants = $product->getVariants();

                if (count($productVariants) > 0) {
                    /** @var ProductVariant $productVariant */
                    foreach ($productVariants as $productVariant) {
                        $variants[] = array(
                            'id' => (string)$productVariant->getId(),
                            'title' => $productVariant->getName() ? $productVariant->getName() : $product->getName()
                        );
                    }
                } else {
                    $variants[] = array(
                        'id' => (string)$product->getId(),
                        'title' => $product->getName(),
                    );
                }

                foreach ($product->getChannels() as $channel)
                {
                    $storeId = $channel->getCode();
                    $response = $this->mailchimpService->getProduct($storeId, $productId);

                    if (isset($response['id'])) {
                        $data = array(
                            'title' => $product->getName(),
                            'variants' => $variants
                        );

                        $this->mailchimpService->updateProduct($storeId, $productId, $data);
                    } else {
                        $data = array(
                            'id' => (string)$productId,
                            'title' => $product->getName(),
                            'variants' => $variants
                        );

                        $this->mailchimpService->addProduct($storeId, $data);
                    }
                }
            }
        }catch (\Exception $e) {
        }
    }

    public function deleteProduct(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $productId = $product->getId();

            foreach ($product->getChannels() as $channel)
            {
                $storeId = $channel->getCode();

                $response = $this->mailchimpService->getProduct($storeId, $productId);

                if (isset($response['id']))
                {
                    $this->mailchimpService->removeProduct($storeId, $productId);
                }
            }
        }
    }
}
