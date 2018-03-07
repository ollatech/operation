<?php
namespace Olla\Operation\Core;

use Olla\Prisma\Metadata;
use Olla\Operation\Resolver;
use Olla\Operation\Response;
use Olla\Operation\Firewall;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Olla\Flow\View;
use Olla\Theme\View as Theme;

final class Operation implements Resolver
{

    protected $container;
    protected $metatada;
    protected $firewall;
    protected $converter;
    protected $theme;

    public function __construct(ContainerInterface $container, Metadata $metadata, Firewall $firewall, Response $converter, Theme $theme) {
        $this->container = $container;
        $this->metadata = $metadata;
        $this->firewall = $firewall;
        $this->converter = $converter;
        $this->theme = $theme;
    }

    public function resolve(array $args = [], $request) {
        if(!$this->firewall->canAccess($args['operation_id'])) {
            throw new Exception("Access Denied", 1);
        }
        $props = [];
        if(null !== $operation = $this->operation($args['carrier'], $args['operation_id'])) {
            if(null === $controllerId = $operation->getController()) {
                return;
            }
            $controller = $this->service($controllerId);
            if (is_callable($controller))
            {
                $result = call_user_func_array($controller, [$operation, $request]);
                if($result instanceof View) {
                    return $result;
                }
                if(!is_array($result)) {
                    throw new \Exception(sprintf("%s Should return an array", $controllerId));
                }
                $props = array_merge($props, $result);
            }
        } 

        switch ($args['format']) {
            case 'html':
            return $this->view($operation, $args, $props);
            break;
            default:
            return $this->converter->render($args, $props);
            break;
        }
    }
    private function view($operation, $args, $props) {
        $template = $operation->getTemplate();
        $assets = $operation->getAssets();
        $react = $operation->getReact();
        $options = $operation->getOptions();
        $context = [
            'resource' => $operation->getResource(),
            'action' => $operation->getAction()
        ];
        return $this->theme->render($template, $props, $assets, $react, $options, $context);
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
