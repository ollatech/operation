<?php

namespace Olla\Operation\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ViewCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
    	$service = $container->findDefinition('olla.response');
        $taggesAction = $container->findTaggedServiceIds('olla.format', true);
        foreach ($taggesAction as $id => $tags) {
            $serviceId = $id;
            $alias = null;
          
            foreach ($tags as $tag) {
                if(isset($tag['alias'])) {
                    $alias = $tag['alias'];
                }
               
            }
            if(!$context || !$design) {
                continue;
            }
            $action = $container->findDefinition($id);
            $action->setPublic(true);
            $service->addMethodCall(
                'addFormat', [$alias, $serviceId]
            );
        }

    }
}
