<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Providers\Contacts\Factory;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Services\Service;

final class ControllerProvider extends Provider implements Factory
{
    public function get(Contextual $contextual, string $name): Service|Repository|Cacheable
    {
        return match ($name) {
            'repository', 'service', 'cache' => $this->getConcrete($contextual->getAlias(), $name),
            default                          => throw new FailedResolveException('The property call failed,[' . $name . 'not exists.'),
        };
    }

    final public function getModel(string $abstract, string $final): string
    {
        if (!$this->has() && str_ends_with($this->model, $final)) {
            return $this->model;
        }

        $clazz = str_replace('\\http\\Controllers\\', '\\', $abstract);

        $parts = explode('\\', $clazz);

        $ns = match ($final) {
            'service'    => array_shift($parts) . '\\Services\\',
            'repository' => array_shift($parts) . '\\Repositories\\',
            'request'    => array_shift($parts) . '\\Http\\Requests\\',
            'cache'      => array_shift($parts) . '\\Cache\\',
        };

        $controller = end($parts);

        $class = $this->str->split($controller, 'Controller');

        $concrete = $ns . $class . ucwords($final);

        if (!class_exists($concrete)) {
            $this->modelNotExistsFail($concrete);
        }

        return $this->refresh($concrete);
    }

    protected function registerAccessor(): array|string
    {
        return 'Controller';
    }

    protected function getConcreteParentAccessor(): string
    {
        return '';
    }
}