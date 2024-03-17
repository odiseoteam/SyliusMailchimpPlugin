<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\CartRegisterHandlerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncCartsCommand extends BaseSyncCommand
{
    public function __construct(
        private EntityRepository $orderRepository,
        private CartRegisterHandlerInterface $cartRegisterHandler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('odiseo:mailchimp:sync-carts')
            ->setDescription('Synchronize the carts to Mailchimp.')
            ->addOption(
                'create-only',
                'c',
                InputOption::VALUE_NONE,
                'With this option the existing carts will be not updated.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the carts to Mailchimp');

        $this->registerCarts($input);

        return 0;
    }

    protected function registerCarts(InputInterface $input): void
    {
        /** @var bool $createOnly */
        $createOnly = $input->getOption('create-only');

        /** @var array $orders */
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
                $response = $this->cartRegisterHandler->register($order, $createOnly);

                if (!isset($response['id'])) {
                    $this->showError($response);
                }
            } catch (\Exception $e) {
                $this->io->error($e->getMessage());
            }

            $this->io->progressAdvance();
        }

        $this->io->progressFinish();
        $this->io->success('The carts has been synchronized successfully.');
    }
}
