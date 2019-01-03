<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseSyncCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @param array $response
     */
    protected function showError(array $response)
    {
        $this->io->error('Status: ' . $response['status'] . ', Detail: ' . $response['detail']);

        if (isset($response['errors']) && count($response['errors']) > 0) {
            foreach ($response['errors'] as $error) {
                $this->io->listing($error);
            }
        }
    }
}
