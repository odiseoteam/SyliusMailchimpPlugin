<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\Command;

use Odiseo\SyliusMailchimpPlugin\Api\EcommerceInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddProductsCommand extends Command
{
    /**
     * @var EcommerceInterface
     */
    protected $ecommerceApi;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param EcommerceInterface $ecommerceApi
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        EcommerceInterface $ecommerceApi,
        ProductRepositoryInterface $productRepository
    )
    {
        parent::__construct();

        $this->ecommerceApi = $ecommerceApi;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('odiseo:mailchimp:add-products')
            ->setDescription('Add Products data to Mailchimp Ecommerce')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addProducts($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function addProducts(OutputInterface $output)
    {
        $output->writeln('Add products in store to Mailchimp...');

        try {
            /** @var ProductInterface $product */
            foreach ($this->productRepository->findAll() as $product) {
                $channels = $product->getChannels();

                foreach($channels as $channel) {
                    $storeId = $channel->getCode();

                    $variants = [];

                    /** @var ProductVariantInterface $productVariant */
                    foreach ($product->getVariants() as $productVariant) {
                        $variants[] = array(
                            'id' => (string)$productVariant->getId(),
                            'title' => $productVariant->getName() ? $productVariant->getName() : $product->getName()
                        );
                    }

                    $data = array(
                        'id' => (string)$product->getId(),
                        'title' => $product->getName(),
                        'variants' => $variants
                    );

                    $response = $this->ecommerceApi->addProduct($storeId, $data);

                    if (isset($response['id'])) {
                        $output->writeln('Register product '.$response['title'].' to '.$storeId.' completed!');
                        $output->writeln('Id: '.$response['id']);
                    } else {
                        $output->writeln('Register product failed to '.$storeId.'!');
                        $output->writeln('Status: '.$response['status'].', Detail: '.$response['detail']);
                    }
                }
            }
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
