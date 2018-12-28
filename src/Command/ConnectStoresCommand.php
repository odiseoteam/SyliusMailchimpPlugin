<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\StoreRegisterHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConnectStoresCommand extends Command
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
            ->setName('odiseo:mailchimp:connect-stores')
            ->setDescription('Connect the Sylius stores (channels) to Mailchimp.')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->connectStores();
    }

    protected function connectStores()
    {
        $this->io->title('Connecting the stores to Mailchimp.');

        try {
            $channels = $this->channelRepository->findBy([
                'enabled' => true
            ]);

            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                $this->io->write('Connecting the "'.$channel->getName().'" store ...');

                $response = $this->storeRegisterHandler->register($channel);

                if (isset($response['id'])) {
                    $this->io->writeln('Done.');
                } else {
                    $this->io->writeln('Error.');
                    $this->io->error('Status: '.$response['status'].', Detail: '.$response['detail']);
                }
            }

            $this->io->success('Store connected successfully.');
        } catch(\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }
}
