<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncAllCommand extends BaseSyncCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('odiseo:mailchimp:sync-all')
            ->setDescription('Synchronize all data to Mailchimp.')
            ->addOption('create-only', 'c', InputOption::VALUE_NONE, 'With this option the existing content will be not updated.')
            ->addOption('purge', 'p', InputOption::VALUE_NONE, 'Reset all the content and reload from scratch.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $withPurge = $input->getOption('purge');
        $createOnly = $input->getOption('create-only');

        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing all data to Mailchimp');

        $application = $this->getApplication();

        if ($application) {
            $syncStoresCommand = $application->find('odiseo:mailchimp:sync-stores');
            $syncCustomersCommand = $application->find('odiseo:mailchimp:sync-customers');
            $syncProductsCommand = $application->find('odiseo:mailchimp:sync-products');
            $syncCartsCommand = $application->find('odiseo:mailchimp:sync-carts');
            $syncOrdersCommand = $application->find('odiseo:mailchimp:sync-orders');

            $syncStoresCommand->run(new ArrayInput([
                '--isSyncing' => true,
                '--purge' => $withPurge,
            ]), $output);

            $syncCustomersCommand->run(new ArrayInput([
                '--create-only' => $createOnly,
            ]), $output);
            $syncProductsCommand->run(new ArrayInput([
                '--create-only' => $createOnly,
            ]), $output);
            $syncCartsCommand->run(new ArrayInput([
                '--create-only' => $createOnly,
            ]), $output);
            $syncOrdersCommand->run(new ArrayInput([
                '--create-only' => $createOnly,
            ]), $output);

            $syncStoresCommand->run(new ArrayInput([
                '--isSyncing' => false,
            ]), $output);
        }

        return 0;
    }
}
