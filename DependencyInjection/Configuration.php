<?php

namespace UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('user');

        $rootNode
            ->children()
                ->scalarNode('user_class')->cannotBeEmpty()->end()
                ->scalarNode('sender_email')->cannotBeEmpty()->end()
                ->arrayNode('login')
                    ->children()
                        ->scalarNode('default_target_path')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('resetting')
                    ->children()
                        ->scalarNode('ttl')->defaultValue(86400)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
