<?php

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Mailchimp\Mailchimp;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailchimpEcommerceCreateStoreCommand extends Command
{
    /**
     *@var Mailchimp
     */
    protected $mailchimp;

    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var string
     */
    protected $listId;

    /**
     * @param Mailchimp $mailchimp
     * @param ChannelRepositoryInterface $channelRepository
     * @param string $listId
     */
    public function __construct(Mailchimp $mailchimp, ChannelRepositoryInterface $channelRepository, string $listId)
    {
        parent::__construct();

        $this->mailchimp = $mailchimp;
        $this->channelRepository = $channelRepository;
        $this->listId = $listId;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:create-stores')
            ->setDescription('Register the Stores to Mailchimp Ecommerce')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerStore($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function registerStore(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Registering the stores to MailChimp.');

        try {
            $channels = $this->channelRepository->findBy([
                'enabled' => true
            ]);

            /** @var ChannelInterface $channel */
            foreach($channels as $channel) {
                $output->writeln('Registering the channel: '.$channel->getName());
                $data = [
                    'id' => $input->getArgument('id'),
                    'list_id' => $this->listId,
                    'name' => $channel->getName(),
                    'currency_code' => $channel->getBaseCurrency()->getCode(),
                ];

                $response = $this->mailchimp->addStore($data);

                if (isset($response['id'])) {
                    $output->writeln('Register store '.$response['name'].' completed!');
                    $output->writeln('Id: '.$response['id']);
                } else {
                    $output->writeln('Register store failed!');
                    $output->writeln('Status: '.$response['status'].', Detail: '.$response['detail']);
                }
            }
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}