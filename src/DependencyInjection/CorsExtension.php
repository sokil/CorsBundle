<?php

namespace Sokil\CorsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CorsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // configure cors listener
        $container
            ->getDefinition('sokil.cors.event_listener.cors_request_listener')
            ->replaceArgument(0, $config['allowedOrigins'])
            ->replaceArgument(1, $container['withCredentials'])
            ->replaceArgument(2, $container['maxAge']);
    }
}
