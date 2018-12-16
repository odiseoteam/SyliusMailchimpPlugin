<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;

class MailchimpEcommerceProductSubscriber implements EventSubscriber
{
    /**
     * @var MailchimpInterface
     */
    protected $mailchimp;

    /**
     * @param MailchimpInterface $mailchimp
     */
    public function __construct(MailchimpInterface $mailchimp)
    {
        $this->mailchimp = $mailchimp;
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
            $this->registerProduct($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->registerProduct($product);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();

        if ($product instanceof ProductInterface) {
            $this->deleteProduct($product);
        }
    }

    /**
     * @param ProductInterface $product
     */
    public function registerProduct(ProductInterface $product)
    {
        try {
            $productId = $product->getId();

            $variants = [];
            /** @var ProductVariant $productVariant */
            foreach ($product->getVariants() as $productVariant) {
                $variants[] = array(
                    'id' => (string)$productVariant->getId(),
                    'title' => $productVariant->getName() ? $productVariant->getName() : $product->getName(),
                );
            }

            foreach ($product->getChannels() as $channel)
            {
                $storeId = $channel->getCode();
                $response = $this->mailchimp->getProduct($storeId, $productId);

                if (isset($response['id'])) {
                    $data = [
                        'title' => $product->getName(),
                        'variants' => $variants,
                    ];

                    $this->mailchimp->updateProduct($storeId, $productId, $data);
                } else {
                    $data = [
                        'id' => (string)$productId,
                        'title' => $product->getName(),
                        'variants' => $variants,
                    ];

                    $this->mailchimp->addProduct($storeId, $data);
                }
            }
        }catch (\Exception $e) {}
    }

    /**
     * @param ProductInterface $product
     */
    public function deleteProduct(ProductInterface $product)
    {
        $productId = $product->getId();

        foreach ($product->getChannels() as $channel) {
            $storeId = $channel->getCode();

            $response = $this->mailchimp->getProduct($storeId, $productId);

            if (isset($response['id'])) {
                $this->mailchimp->removeProduct($storeId, $productId);
            }
        }
    }
}
