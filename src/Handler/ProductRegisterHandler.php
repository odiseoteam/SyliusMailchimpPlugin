<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class ProductRegisterHandler
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @param EcommerceInterface $ecommerceApi
     */
    public function __construct(
        EcommerceInterface $ecommerceApi
    ) {
        $this->ecommerceApi = $ecommerceApi;
    }

    /**
     * @param ProductInterface $product
     */
    public function register(ProductInterface $product)
    {
        $productId = (string)$product->getId();

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
            $response = $this->ecommerceApi->getProduct($storeId, $productId);

            Assert::keyExists($response, 'status');

            if ($response['status'] === Response::HTTP_NOT_FOUND) {
                $data = [
                    'id' => $productId,
                    'title' => $product->getName(),
                    'variants' => $variants,
                ];

                $this->ecommerceApi->addProduct($storeId, $data);
            } else {
                $data = [
                    'title' => $product->getName(),
                    'variants' => $variants,
                ];

                $this->ecommerceApi->updateProduct($storeId, $productId, $data);
            }
        }
    }

    /**
     * @param ProductInterface $product
     */
    public function unregister(ProductInterface $product)
    {
        $productId = (string)$product->getId();

        foreach ($product->getChannels() as $channel) {
            $storeId = $channel->getCode();

            $response = $this->ecommerceApi->getProduct($storeId, $productId);

            Assert::keyExists($response, 'status');

            if ($response['status'] !== Response::HTTP_NOT_FOUND) {
                $this->ecommerceApi->removeProduct($storeId, $productId);
            }
        }
    }
}
