<?php
namespace Olla\Operation\Resolver;

use Olla\Prisma\Metadata;
use Olla\Operation\Resolver;
use Olla\Operation\Response;
use Olla\Operation\Firewall;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Operation implements Resolver
{

    protected $container;
    protected $metatada;
    protected $firewall;
    protected $response;

    public function __construct(ContainerInterface $container, Metadata $metadata, Firewall $firewall, Response $response) {
        $this->container = $container;
        $this->metadata = $metadata;
        $this->firewall = $firewall;
        $this->response = $response;
    }

    public function resolve(string $carrier, string $operationId, array $args = []) {
        if(!$this->firewall->canAccess($operationId)) {
            throw new Exception("Access Denied", 1);
        }
        $response = [];
        if(null !== $controller = $this->find($carrier, $operationId)) {
            if (is_callable($controller))
            {
                $result = call_user_func_array($controller, [$args]);
                $response = array_merge($response, $result);
            }
        } 
        return $this->response->render($carrier, $operationId, $response);
    }
  
    private function find(string $carrier, string $operationId) {
        $operation = null;
        switch ($carrier) {
            case 'restapi':
            $operation = $this->metadata->operation($operationId);
            break;
            case 'graphql':
            $operation = $this->metadata->operation($operationId);
            break;
            case 'frontend':
            $operation = $this->metadata->frontend($operationId);
            break;
            case 'admin':
            $operation = $this->metadata->admin($operationId);
            break;
            default:
            $operation = $this->metadata->operation($operationId);
            break;
        }
        if(null === $operation) {
            return;
        }
        if(null === $serviceId = $operation->getController()) {
            return;
        }
        if(null !== $service = $this->service($serviceId)) {
            return $service;
        }
        return;
    }

    protected function service(string $serviceId) {
        if ($this->container->has($serviceId))
        {
            return $this->container->get($serviceId);
        } 
        throw new \Exception(sprintf("%s not exist on service", $serviceId));
    }
}
