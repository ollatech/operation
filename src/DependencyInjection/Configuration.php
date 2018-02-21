<?php
namespace Olla\Operation\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('olla_platform');
        $rootNode
            ->children()
                ->arrayNode('provider')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('database')->end()
                        ->scalarNode('monitor')->end()
                        ->scalarNode('serializer')->end()
                        ->scalarNode('validator')->end()
                        ->scalarNode('guard')->end()
                        ->scalarNode('gate')->end()
                        ->scalarNode('middleware')->end()
                        ->scalarNode('credential')->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
