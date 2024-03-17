<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $isRequest = $event->isMainRequest();
        if (!$isRequest) {
            return;
        }

        $request = $event->getRequest();

        $session = $request->getSession();
        if (null === $session->get('campaignId')) {
            if (null !== $request->query->get('mc_cid')) {
                $session->set('campaignId', $request->query->get('mc_cid'));
            }
        }
    }
}
