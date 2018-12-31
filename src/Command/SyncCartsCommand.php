<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\CartRegisterHandler;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCartsCommand extends Command
{
    /**
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @var CartRegisterHandler
     */
    protected $cartRegisterHandler;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @param EntityRepository $orderRepository
     * @param CartRegisterHandler $cartRegisterHandler
     */
    public function __construct(
        EntityRepository $orderRepository,
        CartRegisterHandler $cartRegisterHandler
    )
    {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->cartRegisterHandler = $cartRegisterHandler;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:sync-carts')
            ->setDescription('Synchronize the carts to Mailchimp.')
        ;
    }

    /**
     * @inheritdoc
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
            ->leftJoin('o.customer', 'c')
            ->where("c.email = 'songecko@gmail.com'")
            //->where('o.customer IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->io->text('Connecting '.count($orders).' carts.');
        $this->io->progressStart(count($orders));

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            try {
                $response = $this->cartRegisterHandler->register($order);

                if (!isset($response['id']) && $response !== false) {
                    $this->showError($response);
                }
            } catch(\Exception $e) {
                $this->io->error($e->getMessage());
            }

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
        $this->io->success('The carts has been synchronized successfully.');
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
