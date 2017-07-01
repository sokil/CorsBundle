<?php

namespace Sokil\CorsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cors');

        $rootNode
            ->children()
                ->arrayNode('allowedOrigins')
                    ->prototype('scalar')->end()
                ->end()
                ->booleanNode('withCredentials')
                    ->defaultFalse()
                ->end()
                ->integerNode('maxAge')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
