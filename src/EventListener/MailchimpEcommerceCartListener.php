<?php

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\RouterInterface;

class MailchimpEcommerceCartListener
{
    /**
     * @var MailchimpInterface
     */
    protected $mailchimp;

    /**
     * @var CachedPerRequestChannelContext $channelContext
     */
    protected $channelContext;

    /**
     * @var  RouterInterface
     */
    protected $router;

    /**
     * @param MailchimpInterface $mailchimp
     * @param CachedPerRequestChannelContext $channelContext
     * @param RouterInterface $router
     */
    public function __construct(MailchimpInterface $mailchimp, CachedPerRequestChannelContext $channelContext, RouterInterface $router)
    {
        $this->mailchimp = $mailchimp;
        $this->channelContext = $channelContext;
        $this->router = $router;
    }

    /**
     * @param OrderInterface $order
     */
    public function registerCart(OrderInterface $order)
    {
        try {
            $channel = $this->channelContext->getChannel();
            $storeId = $channel->getCode();

            $cartId = $order->getId();

            $routeName = $this->router->generate('brandsof_checkout_start', array('checkout' => $cartId), true);

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

            $response = $this->mailchimp->getCart($storeId, $cartId);

            if (isset($response['id'])) {
                $data = [
                    'order_total' => $order->getTotal(),
                    'checkout_url' => $routeName,
                    'lines' => $lines
                ];

                $this->mailchimp->updateCart($storeId, $cartId, $data);
            } else
            {
                /** @var CustomerInterface $customer */
                if (null === $customer = $order->getCustomer()) {
                    return;
                }

                $data = [
                    'id' => (string)$cartId,
                    'customer' => [
                        'id' => (string)$customer->getId(),
                        'email_address' => $customer->getEmail(),
                        'opt_in_status' => false,
                        'first_name' => $customer->getFirstName()?:'-',
                        'last_name' => $customer->getLastName()?:'-'
                    ],
                    'currency_code' => 'USD',
                    'order_total' => $order->getTotal(),
                    'checkout_url' => $routeName,
                    'lines' => $lines,
                ];

                $this->mailchimp->addCart($storeId, $data);
            }
        } catch (\Exception $e)
        {}
    }
}
