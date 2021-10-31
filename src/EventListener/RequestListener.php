<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $session = $request->getSession();
        if (null === $session->get('campaingId')) {
            if (null !== $request->get('mc_cid')) {
                $session->set('campaingId', $request->get('mc_cid'));
            }
        }
    }
}
