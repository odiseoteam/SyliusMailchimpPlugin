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

final class SyncCustomersCommand extends BaseSyncCommand
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private CustomerRepositoryInterface $customerRepository,
        private CustomerRegisterHandlerInterface $customerRegisterHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('odiseo:mailchimp:sync-customers')
            ->setDescription('Synchronize the customers to Mailchimp.')
            ->addOption(
                'create-only',
                'c',
                InputOption::VALUE_NONE,
                'With this option the existing customers will be not updated.',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the customers to Mailchimp');

        $this->registerCustomers($input);

        return 0;
    }

    protected function registerCustomers(InputInterface $input): void
    {
        /** @var bool $createOnly */
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

                    if (!isset($response['id'])) {
                        $this->showError($response);
                    }
                } catch (\Exception $e) {
                    $this->io->error($e->getMessage());
                }
            }

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();
        $this->io->success('The customers has been synchronized successfully.');
    }
}
