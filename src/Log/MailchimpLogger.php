<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Log;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

final class MailchimpLogger implements LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
