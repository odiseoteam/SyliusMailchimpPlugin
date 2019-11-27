<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Handler\ProductRegisterHandlerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SyncProductsCommand extends BaseSyncCommand
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductRegisterHandlerInterface */
    private $productRegisterHandler;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductRegisterHandlerInterface $productRegisterHandler
    ) {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->productRegisterHandler = $productRegisterHandler;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('odiseo:mailchimp:sync-products')
            ->setDescription('Synchronize the products to Mailchimp.')
            ->addOption('create-only', 'c', InputOption::VALUE_NONE, 'With this option the existing products will be not updated.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Synchronizing the products to Mailchimp');

        $this->registerProducts($input);
    }

    /**
     * @param InputInterface $input
     */
    protected function registerProducts(InputInterface $input): void
    {
        $createOnly = $input->getOption('create-only');

        $products = $this->productRepository->findAll();

        $this->io->text('Connecting ' . count($products) . ' products.');
        $this->io->progressStart(count($products));

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $channels = $product->getChannels();

            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                try {
                    $response = $this->productRegisterHandler->register($product, $channel, $createOnly);

                    if (!isset($response['id']) && $response !== false) {
                        $this->showError($response);
                    }

                    $this->io->progressAdvance(1);
                } catch (\Exception $e) {
                    $this->io->error($e->getMessage());
                }
            }
        }

        $this->io->progressFinish();
        $this->io->success('The products has been synchronized successfully.');
    }
}
