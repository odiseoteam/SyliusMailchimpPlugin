<?php

declare(strict_types=1);

namespace Odiseo\SyliusMailchimpPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('odiseo_sylius_mailchimp_plugin');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
