<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Odiseo\SyliusMailchimpPlugin\Model\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class StoreRegisterHandler implements StoreRegisterHandlerInterface
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @var ListIdProviderInterface
     */
    protected $listIdProvider;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param ListIdProviderInterface $listIdProvider
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        ListIdProviderInterface $listIdProvider
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->listIdProvider = $listIdProvider;
    }

    /**
     * @inheritdoc
     */
    public function register(ChannelInterface $channel)
    {
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);
        $isNew = !isset($response['id']);

        $localeCode = 'en';
        $currencyCode = 'USD';

        if ($defaultLocale = $channel->getDefaultLocale()) {
            $localeCode = $defaultLocale->getCode();
        }

        if ($baseCurrency = $channel->getBaseCurrency()) {
            $currencyCode = $baseCurrency->getCode();
        }

        $data = [
            'id' => $storeId,
            'list_id' => $this->getListIdByChannel($channel),
            'name' => $channel->getName(),
            'domain' => $channel->getHostname(),
            'email_address' => $channel->getContactEmail(),
            'primary_locale' => $localeCode,
            'currency_code' => $currencyCode,
        ];

        if ($isNew) {
            $response = $this->ecommerceApi->addStore($data);
        } else {
            $response = $this->ecommerceApi->updateStore($storeId, $data);
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function unregister(ChannelInterface $channel)
    {
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            return $this->ecommerceApi->removeStore($storeId);
        }

        return false;
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return string
     */
    private function getListIdByChannel(ChannelInterface $channel): string
    {
        if ($channel instanceof MailchimpListIdAwareInterface) {
            if ($listId = $channel->getListId())
                return $listId;
        }

        return $this->listIdProvider->getListId();
    }
}
