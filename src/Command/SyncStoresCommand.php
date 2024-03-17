<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncStoresCommand extends BaseSyncCommand
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private StoreRegisterHandlerInterface $storeRegisterHandler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('odiseo:mailchimp:sync-stores')
            ->setDescription('Synchronize the Sylius stores (channels) to Mailchimp.')
            ->addOption('purge', 'p', InputOption::VALUE_NONE, 'Remove all stores before create the new ones.')
            ->addOption('isSyncing', 's', InputOption::VALUE_NONE, 'Mark the stores with "is_syncing" field like true.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the stores to Mailchimp');

        $this->registerStores($input);

        return 0;
    }

    protected function registerStores(InputInterface $input): void
    {
        /** @var bool $withPurge */
        $withPurge = $input->getOption('purge');
        /** @var bool $isSyncing */
        $isSyncing = $input->getOption('isSyncing');

        $channels = $this->channelRepository->findBy([
            'enabled' => true,
        ]);

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            /** @var string $channelName */
            $channelName = $channel->getName();

            if ($withPurge) {
                $this->io->write('Removing the "' . $channelName . '" store...');

                try {
                    $this->storeRegisterHandler->unregister($channel);
                    $this->io->writeln('Done.');
                } catch (\Exception $e) {
                    $this->io->writeln('Error.');
                    $this->io->error($e->getMessage());
                }
            }

            $this->io->write(
                'Connecting the "' . $channelName . '" store with is_syncing = ' .
                ($isSyncing ? 'true' : 'false') . '...'
            );

            try {
                $response = $this->storeRegisterHandler->register($channel, $isSyncing);

                if (isset($response['id'])) {
                    $this->io->writeln('Done.');
                } else {
                    $this->io->writeln('Error.');

                    $this->showError($response);
                }
            } catch (\Exception $e) {
                $this->io->writeln('Error.');
                $this->io->error($e->getMessage());
            }
        }

        $this->io->success('The stores has been synchronized successfully.');
    }
}
