<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class RequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $session = $request->getSession();
        if ($session !== null) {
            if (!$session->get('campaingId')) {
                if ($request->get('mc_cid')) {
                    $session->set('campaingId', $request->get('mc_cid'));
                }
            }
        }
    }
}
