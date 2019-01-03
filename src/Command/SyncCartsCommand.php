<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\CartRegisterHandlerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCartsCommand extends BaseSyncCommand
{
    /**
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @var CartRegisterHandlerInterface
     */
    protected $cartRegisterHandler;

    /**
     * @param EntityRepository $orderRepository
     * @param CartRegisterHandlerInterface $cartRegisterHandler
     */
    public function __construct(
        EntityRepository $orderRepository,
        CartRegisterHandlerInterface $cartRegisterHandler
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->cartRegisterHandler = $cartRegisterHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:sync-carts')
            ->setDescription('Synchronize the carts to Mailchimp.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the carts to Mailchimp');

        $this->registerCarts();
    }

    protected function registerCarts()
    {
        $orders = $this->orderRepository->createQueryBuilder('o')
            ->where('o.customer IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->io->text('Connecting ' . count($orders) . ' carts.');
        $this->io->progressStart(count($orders));

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            try {
                $response = $this->cartRegisterHandler->register($order);

                if (!isset($response['id']) && $response !== false) {
                    $this->showError($response);
                }
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
            }

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
        $this->io->success('The carts has been synchronized successfully.');
    }
}
