<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Controller\Action;

use Odiseo\SyliusMailchimpPlugin\Handler\CustomerNewsletterSubscriptionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubscribeToNewsletterAction
{
    /** @var CustomerNewsletterSubscriptionHandler */
    private $customerNewsletterSubscriptionHandler;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        CustomerNewsletterSubscriptionHandler $customerNewsletterSubscriptionHandler,
        TranslatorInterface $translator
    ) {
        $this->customerNewsletterSubscriptionHandler = $customerNewsletterSubscriptionHandler;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $email = $request->request->get('email');

        $this->customerNewsletterSubscriptionHandler->subscribe($email);

        return new JsonResponse([
            'success' => true,
            'message' => $this->translator->trans('odiseo_sylius_mailchimp_plugin.ui.subscribed_successfully'),
        ]);
    }
}
