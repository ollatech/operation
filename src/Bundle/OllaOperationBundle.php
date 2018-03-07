<?php
namespace Olla\Operation\Bundle;

use Olla\Operation\Bundle\Compiler\ViewCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OllaOperationBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
		$container->addCompilerPass(new ViewCompilerPass());
	}
}
