<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseSyncCommand extends Command
{
    protected SymfonyStyle $io;

    protected function showError(array $response): void
    {
        /** @var string $status */
        $status = $response['status'];
        /** @var string $detail */
        $detail = $response['detail'];

        $this->io->error('Status: ' . $status . ', Detail: ' . $detail);

        if (isset($response['errors']) && count($response['errors']) > 0) {
            foreach ($response['errors'] as $error) {
                $this->io->listing($error);
            }
        }
    }
}
