<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncStoresCommand extends Command
{
    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var StoreRegisterHandlerInterface
     */
    protected $storeRegisterHandler;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param StoreRegisterHandlerInterface $storeRegisterHandler
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        StoreRegisterHandlerInterface $storeRegisterHandler
    )
    {
        parent::__construct();

        $this->channelRepository = $channelRepository;
        $this->storeRegisterHandler = $storeRegisterHandler;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:sync-stores')
            ->setDescription('Synchronize the Sylius stores (channels) to Mailchimp.')
            ->addOption('purge', 'p', InputOption::VALUE_NONE, 'Remove all stores before create the new ones.')
            ->addOption('isSyncing', 's', InputOption::VALUE_NONE, 'Mark the stores with "is_syncing" field like true.')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the stores to Mailchimp');

        $this->registerStores($input);
    }

    /**
     * @param InputInterface $input
     */
    protected function registerStores(InputInterface $input)
    {
        $withPurge = $input->getOption('purge');
        $isSyncing = $input->getOption('isSyncing');

        $channels = $this->channelRepository->findBy([
            'enabled' => true
        ]);

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            if ($withPurge) {
                $this->io->write('Removing the "'.$channel->getName().'" store...');

                try {
                    $this->storeRegisterHandler->unregister($channel);
                    $this->io->writeln('Done.');
                } catch (\Exception $e) {
                    $this->io->writeln('Error.');
                    $this->io->error($e->getMessage());
                }
            }

            $this->io->write('Connecting the "'.$channel->getName().'" store...');

            try {
                $response = $this->storeRegisterHandler->register($channel, $isSyncing);

                if (isset($response['id'])) {
                    $this->io->writeln('Done.');
                } else {
                    $this->io->writeln('Error.');
                    if ($response !== false) {
                        $this->showError($response);
                    }
                }
            } catch(\Exception $e) {
                $this->io->writeln('Error.');
                $this->io->error($e->getMessage());
            }
        }

        $this->io->success('The stores has been synchronized successfully.');
    }

    /**
     * @param array $response
     */
    private function showError(array $response)
    {
        $this->io->error('Status: '.$response['status'].', Detail: '.$response['detail']);

        if (isset($response['errors']) && count($response['errors']) > 0) {
            foreach ($response['errors'] as $error) {
                $this->io->listing($error);
            }
        }
    }
}
