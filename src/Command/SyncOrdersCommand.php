<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\OrderRegisterHandlerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncOrdersCommand extends Command
{
    /**
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @var OrderRegisterHandlerInterface
     */
    protected $orderRegisterHandler;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @param EntityRepository $orderRepository
     * @param OrderRegisterHandlerInterface $orderRegisterHandler
     */
    public function __construct(
        EntityRepository $orderRepository,
        OrderRegisterHandlerInterface $orderRegisterHandler
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->orderRegisterHandler = $orderRegisterHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:sync-orders')
            ->setDescription('Synchronize the orders to Mailchimp.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the orders to Mailchimp');

        $this->registerOrders();
    }

    protected function registerOrders()
    {
        $orders = $this->orderRepository->createQueryBuilder('o')
            ->andWhere('o.paymentState = :paymentState')
            ->setParameter('paymentState', OrderPaymentStates::STATE_PAID)
            ->getQuery()
            ->getResult()
        ;

        $this->io->text('Connecting ' . count($orders) . ' carts.');
        $this->io->progressStart(count($orders));

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            try {
                $response = $this->orderRegisterHandler->register($order);

                if (!isset($response['id']) && $response !== false) {
                    $this->showError($response);
                }
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
            }

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
        $this->io->success('The orders has been synchronized successfully.');
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
