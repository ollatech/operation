<?php
namespace Olla\Operation;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OllaOperationBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
	
	}
}
