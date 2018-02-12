<?php
namespace Olla\Operation\Core;

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

    public function resolve(array $args = [], $request) {
        if(!$this->firewall->canAccess($args['operation_id'])) {
            throw new Exception("Access Denied", 1);
        }
        $response = [];
        if(null !== $operation = $this->operation($args['carrier'], $args['operation_id'])) {
            if(null === $controllerId = $operation->getController()) {
                return;
            }
            $controller = $this->service($controllerId);
            if (is_callable($controller))
            {
                $result = call_user_func_array($controller, [$operation, $request]);
                if(!is_array($result)) {
                    throw new \Exception(sprintf("%s Should return an array", $controllerId));
                }
                $response = array_merge($response, $result);
            }
        } 
        return $this->response->render($args, $response);
    }

    private function operation(string $carrier, string $operationId) {
        $args = [];
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
        return $operation;
    }

    protected function service(string $serviceId) {
        if ($this->container->has($serviceId))
        {
            return $this->container->get($serviceId);
        } 
        throw new \Exception(sprintf("%s not exist on service", $serviceId));
    }
}
