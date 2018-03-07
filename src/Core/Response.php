<?php
namespace Olla\Operation\Core;


use Olla\Operation\Response as ResponseComponent;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Response implements ResponseComponent
{
	protected $views = [];
	protected $container;
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	public function render(array $args = [], array $response = []) {
		$format = $args['format'];
		$carrier = $args['carrier'];
		$operationId = $args['operation_id'];
		if(isset($this->views[$format])) {
			$serviceId = $this->views[$format];
			if(null !== $service = $this->service($serviceId)) {
				return $service->render($args, $response);
			}
		}
		throw new \Exception("Format renderer not found", 1);
	}

	
	public function addFormat(string $format, $serviceId) {
		$this->views[$format] = $serviceId;
	}
	protected function service(string $serviceId) {
		if ($this->container->has($serviceId))
		{
			return $this->container->get($serviceId);
		} 
		throw new \Exception(sprintf("%s not exist on service", $serviceId));
	}
}
