<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\CustomerRegisterHandlerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCustomersCommand extends Command
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
     * @var SymfonyStyle
     */
    private $io;

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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the customers to Mailchimp');

        $this->registerCustomers();
    }

    protected function registerCustomers()
    {
        $channels = $this->channelRepository->findAll();
        $customers = $this->customerRepository->findAll();

        $this->io->text('Connecting ' . count($customers) . ' customers.');
        $this->io->progressStart(count($customers));

        /** @var CustomerInterface $customer */
        foreach ($customers as $customer) {
            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                try {
                    $response = $this->customerRegisterHandler->register($customer, $channel);

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

    /**
     * @param array $response
     */
    private function showError(array $response)
    {
        $this->io->error('Status: ' . $response['status'] . ', Detail: ' . $response['detail']);

        if (isset($response['errors']) && count($response['errors']) > 0) {
            foreach ($response['errors'] as $error) {
                $this->io->listing($error);
            }
        }
    }
}
