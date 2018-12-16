<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

class MailchimpEcommerceOrderListener
{
    /**
     * @var MailchimpInterface
     */
    protected $mailchimp;
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param MailchimpInterface $mailchimp
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(MailchimpInterface $mailchimp, ChannelContextInterface $channelContext)
    {
        $this->mailchimp = $mailchimp;
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

            /** @var CustomerInterface $customer */
            if (null === $customer = $order->getCustomer()) {
                return;
            }

            $data = [
                'id' => (string)$orderId,
                'customer' => [
                    'id' => (string)$customer->getId(),
                    'email_address' => $customer->getEmail(),
                    'opt_in_status' => false,
                    'first_name' => $customer->getFirstName()?:'-',
                    'last_name' => $customer->getLastName()?:'-'
                ],
                'currency_code' => 'USD',
                'order_total' => $order->getTotal(),
                'lines' => $lines,
            ];

            $this->mailchimp->addOrder($storeId, $data);
        }catch (\Exception $e) {}
    }
}