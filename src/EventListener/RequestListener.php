<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

final class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        /**
         * @psalm-suppress DeprecatedMethod
         * @phpstan-ignore-next-line
         */
        $isRequest = method_exists($event, 'isMainRequest') ? $event->isMainRequest() : $event->isMasterRequest();
        if (!$isRequest) {
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
