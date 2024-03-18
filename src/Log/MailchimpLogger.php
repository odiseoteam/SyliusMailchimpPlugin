<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Log;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

final class MailchimpLogger implements LoggerAwareInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
