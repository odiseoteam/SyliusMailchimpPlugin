<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddCustomersCommand extends Command
{
    /**
     * @var EcommerceInterface
     */
    protected $ecommerceApi;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /** @var SymfonyStyle */
    private $io;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param ChannelContextInterface $channelContext
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        ChannelContextInterface $channelContext,
        CustomerRepositoryInterface $customerRepository
    )
    {
        parent::__construct();

        $this->ecommerceApi = $ecommerceApi;
        $this->channelContext = $channelContext;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:add-customers')
            ->setDescription('Add customers data to Mailchimp Ecommerce')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->addCostumers($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function addCostumers(OutputInterface $output)
    {
        $this->io->title('Adding customers to Mailchimp...');

        try {
            /** @var CustomerInterface $customer */
            foreach ($this->customerRepository->findAll() as $customer) {
                $user = $customer->getUser();

                if ($user instanceof ChannelAwareInterface) {
                    $channel = $user->getChannel();

                    if ($channel) {
                        $storeId = $channel->getCode();

                        $data = [
                            'id' => (string) $customer->getId(),
                            'email_address' => $customer->getEmail(),
                            'opt_in_status' => false,
                            'first_name' => $customer->getFirstName(),
                            'last_name' => $customer->getLastName()
                        ];

                        $response = $this->ecommerceApi->addCustomer($storeId, $data);

                        if (isset($response['id'])) {
                            $output->writeln('Register customer ' . $response['email_address'].' to '.$storeId.' completed!');
                            $output->writeln('Id: ' . $response['id']);
                        } else {
                            $output->writeln('Register customer failed to '.$storeId.'!');
                            $output->writeln('Status: '.$response['status'].', Detail: '.$response['detail']);
                        }
                    } else {
                        $output->writeln('Store not found for customer '.$customer->getId());
                    }
                } else {
                    $output->writeln('Customer '.$customer->getId().' not found');
                }
            }
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
