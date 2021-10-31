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
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProductRegisterHandler implements ProductRegisterHandlerInterface
{
    private EcommerceInterface $ecommerceApi;
    private RouterInterface $router;
    private CacheManager $cacheManager;
    private EventDispatcherInterface $eventDispatcher;
    private bool $enabled;

    public function __construct(
        EcommerceInterface $ecommerceApi,
        RouterInterface $router,
        CacheManager $cacheManager,
        EventDispatcherInterface $eventDispatcher,
        bool $enabled
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->router = $router;
        $this->cacheManager = $cacheManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->enabled = $enabled;
    }

    public function register(ProductInterface $product, ChannelInterface $channel, bool $createOnly = false): array
    {
        if (!$this->enabled) {
            return [];
        }

        $productId = (string) $product->getId();

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getProduct($storeId, $productId);
        $isNew = !isset($response['id']);

        if (false === $isNew && true === $createOnly) {
            return [];
        }

        $variants = [];
        /** @var ProductVariant $productVariant */
        foreach ($product->getVariants() as $productVariant) {
            $variant = [
                'id' => (string) $productVariant->getId(),
                'title' => $productVariant->getName() ?? $product->getName(),
                'inventory_quantity' => $productVariant->isTracked() ? $productVariant->getOnHand() : 1,
            ];

            $variantPrice = $this->getVariantPrice($productVariant, $channel);
            if (null !== $variantPrice) {
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
            'description' => $product->getDescription() ?? '',
            'images' => $productImages,
            'variants' => $variants,
        ];

        if (count($productImages) > 0) {
            $data['image_url'] = $productImages[0]['url'];
        }

        $event = new GenericEvent($product, ['data' => $data, 'channel' => $channel]);
        if ($isNew) {
            $this->eventDispatcher->dispatch($event, 'mailchimp.product.pre_add');
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->addProduct($storeId, $data);
        } else {
            $this->eventDispatcher->dispatch($event, 'mailchimp.product.pre_update');
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->updateProduct($storeId, $productId, $data);
        }

        return $response;
    }

    public function unregister(ProductInterface $product, ChannelInterface $channel): array
    {
        if (!$this->enabled) {
            return [];
        }

        $productId = (string) $product->getId();

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getProduct($storeId, $productId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($product, ['channel' => $channel]);
            $this->eventDispatcher->dispatch($event, 'mailchimp.product.pre_remove');

            return $this->ecommerceApi->removeProduct($storeId, $productId);
        }

        return [];
    }

    private function getVariantPrice(ProductVariant $variant, ChannelInterface $channel): ?int
    {
        $channelPricing = $variant->getChannelPricingForChannel($channel);

        $variantPrice = null;
        if ($channelPricing instanceof ChannelPricingInterface) {
            $variantPrice = $channelPricing->getPrice();
        }

        return $variantPrice;
    }

    private function getProductUrl(ProductInterface $product, ChannelInterface $channel): string
    {
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname() ?? '');

        $locale = 'en';
        $channelDefaultLocale = $channel->getDefaultLocale();

        if (null !== $channelDefaultLocale) {
            $locale = $channelDefaultLocale->getCode();
        } else {
            if (count($channel->getLocales()) > 0) {
                $channelLocale = $channel->getLocales()->first();
                if ($channelLocale instanceof LocaleInterface) {
                    $locale = $channelLocale->getCode();
                }
            }
        }

        $product->setCurrentLocale($locale ?? '');

        return $this->router->generate('sylius_shop_product_show', [
            '_locale' => $locale,
            'slug' => $product->getSlug(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function getImageUrl(ProductImageInterface $image, ChannelInterface $channel): string
    {
        $context = $this->router->getContext();
        $context->setHost($channel->getHostname() ?? '');

        return $this->cacheManager->generateUrl($image->getPath() ?? '', 'sylius_large');
    }
}
