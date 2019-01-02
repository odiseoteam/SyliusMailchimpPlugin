<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Controller\Action;

use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandler;
use Odiseo\SyliusMailchimpPlugin\Model\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubscribeToNewsletterAction
{
    /**
     * @var CustomerNewsletterSubscriptionHandler
     */
    private $customerNewsletterSubscriptionHandler;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var ListIdProviderInterface
     */
    private $listIdProvider;

    /**
     * @param CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler
     * @param TranslatorInterface $translator
     * @param ChannelContextInterface $channelContext
     * @param ListIdProviderInterface $listIdProvider
     */
    public function __construct(
        CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler,
        TranslatorInterface $translator,
        ChannelContextInterface $channelContext,
        ListIdProviderInterface $listIdProvider
    ) {
        $this->customerNewsletterSubscriptionHandler = $customerNewsletterSubscriptionHandler;
        $this->translator = $translator;
        $this->channelContext = $channelContext;
        $this->listIdProvider = $listIdProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $newsletter = $request->request->get('newsletter');
        /** @var string $email */
        $email = $newsletter['email'];

        $customer = new Customer();
        $customer->setEmail($email);

        $listId = $this->getListIdByChannel($this->channelContext->getChannel());

        $response = $this->customerNewsletterSubscriptionHandler->subscribe($customer, $listId);

        if (!isset($response['id'])) {
            return new JsonResponse([
                'success' => false,
                'message' => $response['detail'],
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('odiseo_sylius_mailchimp_plugin.ui.subscribed_successfully'),
        ]);
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return string
     */
    private function getListIdByChannel(ChannelInterface $channel): string
    {
        if ($channel instanceof MailchimpListIdAwareInterface) {
            if ($listId = $channel->getListId()) {
                return $listId;
            }
        }

        return $this->listIdProvider->getListId();
    }
}
