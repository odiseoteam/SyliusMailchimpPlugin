<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Controller\Action;

use Odiseo\SyliusMailchimpPlugin\Entity\MailchimpListIdAwareInterface;
use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandler;
use Odiseo\SyliusMailchimpPlugin\Provider\ListIdProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubscribeToNewsletterAction
{
    public function __construct(
        private CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler,
        private TranslatorInterface $translator,
        private ChannelContextInterface $channelContext,
        private ListIdProviderInterface $listIdProvider,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $newsletter = $request->request->all('newsletter');
        /** @var string $email */
        $email = $newsletter['email'];

        $customer = new Customer();
        $customer->setEmail($email);

        $listId = $this->getListIdByChannel($this->channelContext->getChannel());

        $response = $this->customerNewsletterSubscriptionHandler->subscribe($customer, $listId);

        if (!isset($response['id'])) {
            return new JsonResponse([
                'success' => false,
                'message' => $response['detail'] ?? '',
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('odiseo_sylius_mailchimp_plugin.ui.subscribed_successfully'),
        ]);
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
