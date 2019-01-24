<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCustomersCommand extends BaseSyncCommand
{
    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerRegisterHandlerInterface
     */
    protected $customerRegisterHandler;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegisterHandlerInterface $customerRegisterHandler
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegisterHandlerInterface $customerRegisterHandler
    ) {
        parent::__construct();

        $this->channelRepository = $channelRepository;
        $this->customerRepository = $customerRepository;
        $this->customerRegisterHandler = $customerRegisterHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:sync-customers')
            ->setDescription('Synchronize the customers to Mailchimp.')
            ->addOption('create-only', 'c', InputOption::VALUE_NONE, 'With this option the existing customers will be not updated.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the customers to Mailchimp');

        $this->registerCustomers($input);
    }

    /**
     * @param InputInterface $input
     */
    protected function registerCustomers(InputInterface $input)
    {
        $createOnly = $input->getOption('create-only');

        $channels = $this->channelRepository->findAll();
        $customers = $this->customerRepository->findAll();

        $this->io->text('Connecting ' . count($customers) . ' customers.');
        $this->io->progressStart(count($customers));

        /** @var CustomerInterface $customer */
        foreach ($customers as $customer) {
            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                try {
                    $response = $this->customerRegisterHandler->register($customer, $channel, false, $createOnly);

                    if (!isset($response['id']) && $response !== false) {
                        $this->showError($response);
                    }
                } catch (\Exception $e) {
                    $this->io->error($e->getMessage());
                }
            }

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
        $this->io->success('The customers has been synchronized successfully.');
    }
}
