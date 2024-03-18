<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Odiseo\SyliusMailchimpPlugin\Entity\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class StoreRegisterHandler implements StoreRegisterHandlerInterface
{
    public function __construct(
        private EcommerceInterface $ecommerceApi,
        private ListIdProviderInterface $listIdProvider,
        private EventDispatcherInterface $eventDispatcher,
        private bool $enabled,
    ) {
    }

    public function register(ChannelInterface $channel, bool $isSyncing = false): array
    {
        if (!$this->enabled) {
            return [];
        }

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);
        $isNew = !isset($response['id']);

        $localeCode = 'en';
        $currencyCode = 'USD';

        $defaultLocale = $channel->getDefaultLocale();
        if ($defaultLocale instanceof LocaleInterface) {
            $localeCode = $defaultLocale->getCode();
        }

        $baseCurrency = $channel->getBaseCurrency();
        if ($baseCurrency instanceof LocaleInterface) {
            $currencyCode = $baseCurrency->getCode();
        }

        $data = [
            'id' => $storeId,
            'list_id' => $this->getListIdByChannel($channel),
            'name' => $channel->getName(),
            'platform' => 'Sylius',
            'domain' => $channel->getHostname(),
            'is_syncing' => $isSyncing,
            'email_address' => $channel->getContactEmail(),
            'currency_code' => $currencyCode,
            'primary_locale' => $localeCode,
        ];

        $event = new GenericEvent($channel, ['data' => $data]);
        if ($isNew) {
            $this->eventDispatcher->dispatch($event, 'mailchimp.store.pre_add');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->addStore($data);
        } else {
            $this->eventDispatcher->dispatch($event, 'mailchimp.store.pre_update');
            /** @var array $data */
            $data = $event->getArgument('data');

            $response = $this->ecommerceApi->updateStore($storeId, $data);
        }

        return $response;
    }

    public function unregister(ChannelInterface $channel): array
    {
        if (!$this->enabled) {
            return [];
        }

        /** @var string $storeId */
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);
        $isNew = !isset($response['id']);

        if (!$isNew) {
            $event = new GenericEvent($channel);

            $this->eventDispatcher->dispatch($event, 'mailchimp.store.pre_remove');

            return $this->ecommerceApi->removeStore($storeId);
        }

        return [];
    }

    private function getListIdByChannel(ChannelInterface $channel): string
    {
        if ($channel instanceof MailchimpListIdAwareInterface) {
            $listId = $channel->getListId();
            if (null !== $listId) {
                return $listId;
            }
        }

        return $this->listIdProvider->getListId();
    }
}
