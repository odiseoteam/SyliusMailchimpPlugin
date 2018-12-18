<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Handler;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class StoreRegisterHandler
{
    /**
     * @var EcommerceInterface
     */
    private $ecommerceApi;

    /**
     * @var string
     */
    private $listId;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param string $listId
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        string $listId
    ) {
        $this->ecommerceApi = $ecommerceApi;
        $this->listId = $listId;
    }

    /**
     * @param ChannelInterface $channel
     */
    public function register(ChannelInterface $channel)
    {
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);

        Assert::keyExists($response, 'status');

        if ($response['status'] === Response::HTTP_NOT_FOUND) {
            $data = [
                'id' => $storeId,
                'list_id' => $this->listId,
                'name' => $channel->getName(),
                'currency_code' => $channel->getBaseCurrency()->getCode()
            ];

            $this->ecommerceApi->addStore($data);
        } else {
            $data = [
                'name' => $channel->getName(),
                'currency_code' => $channel->getBaseCurrency()->getCode()
            ];

            $this->ecommerceApi->updateStore($storeId, $data);
        }
    }

    /**
     * @param ChannelInterface $channel
     */
    public function unregister(ChannelInterface $channel)
    {
        $storeId = $channel->getCode();

        $response = $this->ecommerceApi->getStore($storeId);

        Assert::keyExists($response, 'status');

        if ($response['status'] !== Response::HTTP_NOT_FOUND) {
            $this->ecommerceApi->removeStore($storeId);
        }
    }
}
