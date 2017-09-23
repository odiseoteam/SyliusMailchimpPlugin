<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Core\Model\OrderInterface;

class MailchimpEcommerceCartListener
{
    /**
     *@var MailchimpService
     */
    protected $mailchimpService;

    /**
     *@var CachedPerRequestChannelContext $channelContext
     */
    protected $channelContext;

    /**
     * @param MailChimpService $mailchimpService
     * @param CachedPerRequestChannelContext $channelContext
     */
    public function __construct(MailChimpService $mailchimpService, CachedPerRequestChannelContext $channelContext)
    {
        $this->mailchimpService = $mailchimpService;
        $this->channelContext = $channelContext;
    }

    /**
     * @param OrderInterface $order
     */
    public function registerCart(OrderInterface $order)
    {
        try {
            $store = $this->channelContext->getChannel();
            $storeId = $store->getCode();

            $cartId = $order->getId();

            $lines = [];
            $items = $order->getItems();
            foreach ($items as $item) {
                $lines[] = [
                    'id' => (string)$item->getId(),
                    'product_id' => (string)$item->getProduct()->getId(),
                    'product_variant_id' => (string)$item->getVariant()->getId(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getTotal()
                ];
            }

            $response = $this->mailchimpService->getCart($storeId, $cartId);

            if (isset($response['id'])) {
                $data = array(
                    'order_total' => $order->getTotal(),
                    'lines' => $lines
                );

                $this->mailchimpService->updateCart($storeId, $cartId, $data);
            } else
            {
                /** @var CustomerInterface $customer */
                if (null === $customer = $order->getCustomer()) {
                    return;
                }

                $data = array(
                    'id' => (string)$cartId,
                    'customer' => array(
                        'id' => (string)$customer->getId(),
                        'email_address' => $customer->getEmail(),
                        'opt_in_status' => false,
                        'first_name' => $customer->getFirstName()?:'-',
                        'last_name' => $customer->getLastName()?:'-'
                    ),
                    'currency_code' => 'USD',
                    'order_total' => $order->getTotal(),
                    'lines' => $lines
                );

                $this->mailchimpService->addCart($storeId, $data);
            }
        } catch (\Exception $e)
        {}
    }
}
