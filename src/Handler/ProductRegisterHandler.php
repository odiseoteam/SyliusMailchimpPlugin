<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ProductRegisterHandler implements ProductRegisterHandlerInterface
{
    /** @var EcommerceInterface */
    private $ecommerceApi;

    /** @var RouterInterface */
    private $router;

    /** @var CacheManager */
    private $cacheManager;

    /** @var bool */
    private $enabled;

    public function __construct(
        EcommerceInterface $ecommerceApi,
        RouterInterface $router,
        CacheManager $cacheManager,
        bool $enabled
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->router = $router;
        $this->cacheManager = $cacheManager;
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function register(ProductInterface $product, ChannelInterface $channel, bool $createOnly = false)
    {
        if (!$this->enabled) {
            return false;
        }

        $productId = (string) $product->getId();
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getProduct($storeId, $productId);
        $isNew = !isset($response['id']);

        // Do nothing if the product exists
        if (false === $isNew && true === $createOnly) {
            return false;
        }

        $variants = [];
        /** @var ProductVariant $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $variant = [
                'id' => (string) $productVariant->getId(),
                'title' => $productVariant->getName() ? $productVariant->getName() : $product->getName(),
                'inventory_quantity' => $productVariant->isTracked() ? $productVariant->getOnHand() : 1,
            ];

            if ($variantPrice = $this->getVariantPrice($productVariant, $channel)) {
                $variant['price'] = $variantPrice / 100;
            }

            $variants[] = $variant;
        }

        $productImages = [];
        /** @var ProductImageInterface $image */
        foreach ($product->getImages() as $image) {
            $productImages[] = [
                'id' => (string) $image->getId(),
                'url' => $this->getImageUrl($image, $channel),
            ];
        }

        $data = [
            'id' => $productId,
            'title' => $product->getName(),
            'url' => $this->getProductUrl($product, $channel),
            'description' => $product->getDescription() ?: '',
            'images' => $productImages,
            'variants' => $variants,
        ];

        if (count($productImages) > 0) {
            $data['image_url'] = $productImages[0]['url'];
        }

        if ($isNew) {
            $response = $this->ecommerceApi->addProduct($storeId, $data);
        } else {
            $response = $this->ecommerceApi->updateProduct($storeId, $productId, $data);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister(ProductInterface $product, ChannelInterface $channel)
    {
        if (!$this->enabled) {
            return false;
        }

        $productId = (string) $product->getId();
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getProduct($storeId, $productId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeProduct($storeId, $productId);
        }

        return false;
    }

    /**
     * @param ProductVariant $variant
     * @param ChannelInterface $channel
     * @return int|null
     */
    private function getVariantPrice(ProductVariant $variant, ChannelInterface $channel): ?int
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $variant->getChannelPricingForChannel($channel);

        return $channelPricing !== null ? $channelPricing->getPrice() : null;
    }

    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     * @return string
     */
    private function getProductUrl(ProductInterface $product, ChannelInterface $channel): string
    {
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname());

        $locale = 'en';
        $channelDefaultLocale = $channel->getDefaultLocale();

        if (null !== $channelDefaultLocale) {
            $locale = $channelDefaultLocale->getCode();
        } else {
            if (count($channel->getLocales()) > 0) {
                $locale = $channel->getLocales()->first()->getCode();
            }
        }

        $product->setCurrentLocale($locale);

        return $this->router->generate('sylius_shop_product_show', [
            '_locale' => $locale,
            'slug' => $product->getSlug(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param ProductImageInterface $image
     * @param ChannelInterface $channel
     * @return string
     */
    private function getImageUrl(ProductImageInterface $image, ChannelInterface $channel): string
    {
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname());

        return $this->cacheManager->generateUrl($image->getPath(), 'sylius_large');
    }
}
