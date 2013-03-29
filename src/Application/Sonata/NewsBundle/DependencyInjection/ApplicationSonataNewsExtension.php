<?php

namespace Application\Sonata\NewsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as XmlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */

 
class ApplicationSonataNewsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {/*
       $container = new ContainerBuilder();
$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
$loader->load('twig.xml');*/

$configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}


