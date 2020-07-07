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

        $container->setParameter('prodavay_graylog_test.host', $config['host']);
        $container->setParameter('prodavay_graylog_test.port', $config['port']);
    }

    public function getAlias()
    {
        return 'prodavay_graylog_test';
    }
}