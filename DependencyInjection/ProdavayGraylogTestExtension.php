<?php

namespace Prodavay\GraylogTestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ProdavayGraylogTestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        $def = $container->getDefinition('prodavay_graylog_test.service');
        $def->replaceArgument('$loggerHost', $config['host']);
        $def->replaceArgument('$loggerPort', $config['port']);
    }

    public function getAlias()
    {
        return 'prodavay_graylog_test';
    }
}