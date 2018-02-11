<?php
namespace Olla\Operation\Resolver;


use Olla\Operation\Firewall as  FirewallComponent;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Firewall implements FirewallComponent
{
	public function canAccess(string $operationId)
	{
		return true;
	}
}
