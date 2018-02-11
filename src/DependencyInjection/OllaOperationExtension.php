<?php

namespace Olla\Operation\DependencyInjection;


use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Operation\DirectoryOperation;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;


final class OllaOperationExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
       
    }
    public function load(array $configs, ContainerBuilder $container)
    { 
        $this->reconfig($configs, $container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Operations/config'));
        $loader->load('operation.xml');
    }
    private function reconfig(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

    }
}
