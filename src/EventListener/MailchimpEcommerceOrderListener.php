<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Service\MailchimpService;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

class MailchimpEcommerceOrderListener
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
     * @param MailchimpService $mailchimpService
     * @param CachedPerRequestChannelContext $channelContext
     */
    public function __construct(MailchimpService $mailchimpService, CachedPerRequestChannelContext $channelContext)
    {
        $this->mailchimpService = $mailchimpService;
        $this->channelContext = $channelContext;
    }

    /**
     * @param OrderInterface $order
     *
     * @throws UnexpectedTypeException
     */
    public function registerOrder(OrderInterface $order)
    {
        try {
            $store = $this->channelContext->getChannel();
            $storeId = $store->getCode();
            $orderId = $order->getId();

            $lines = array();
            $items = $order->getItems();
            foreach ($items as $item) {
                $lines[] = array(
                    'id' => (string)$item->getId(),
                    'product_id' => (string)$item->getProduct()->getId(),
                    'product_variant_id' => (string)$item->getVariant()->getId(),
                    'quantity' => $item->getQuantity(),
                    'price' => $item->getTotal()
                );
            }

            /** @var CustomerInterface $customer */
            if (null === $customer = $order->getCustomer()) {
                return;
            }

            $data = array(
                'id' => (string)$orderId,
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

            $this->mailchimpService->addOrder($storeId, $data);
        }catch (\Exception $e) {
        }
    }
}