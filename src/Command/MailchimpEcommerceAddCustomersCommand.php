<?php

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\MailchimpInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailchimpEcommerceAddCustomersCommand extends Command
{
    /**
     *@var MailchimpInterface
     */
    protected $mailchimp;

    /**
     *@var ChannelContextInterface
     */
    protected $channelContext;

    /**
     *@var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * MailchimpEcommerceAddCustomersCommand constructor.
     *
     * @param MailchimpInterface $mailchimp
     * @param ChannelContextInterface $channelContext
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        MailchimpInterface $mailchimp,
        ChannelContextInterface $channelContext,
        CustomerRepositoryInterface $customerRepository
    )
    {
        parent::__construct();

        $this->mailchimp = $mailchimp;
        $this->channelContext = $channelContext;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:customers:add')
            ->setDescription('Add Customers in Store to Mailchimp Ecommerce')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addCostumers($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function addCostumers(OutputInterface $output)
    {
        $output->writeln('Add customers in store to Mailchimp...');

        try {
            /** @var CustomerInterface $customer */
            foreach ($this->customerRepository->findAll() as $customer) {
                $user = $customer->getUser();

                if ($user instanceof ChannelAwareInterface) {
                    $channel = $user->getChannel();

                    if ($channel) {
                        $storeId = $channel->getCode();

                        $data = [
                            'id' => (string)$customer->getId(),
                            'email_address' => $customer->getEmail(),
                            'opt_in_status' => false,
                            'first_name' => $customer->getFirstName(),
                            'last_name' => $customer->getLastName()
                        ];

                        $response = $this->mailchimp->addCustomer($storeId, $data);

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