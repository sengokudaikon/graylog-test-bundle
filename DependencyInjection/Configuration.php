<?php

namespace Prodavay\GraylogTestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder $builder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder('prodavay_graylog_test');

        $rootNode = $builder->getRootNode();
        $rootNode->children()
            ->scalarNode('host')
            ->isRequired()
            ->end()
            ->scalarNode('port')
            ->isRequired()
            ->end()
            ->end();

        return $builder;
    }
}
