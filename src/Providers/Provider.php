<?php

namespace Xgbnl\Cloud\Providers;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Xgbnl\Cloud\Contacts\Dominator;
use Xgbnl\Cloud\Exceptions\FailedResolveException;

abstract class Provider
{
    protected array $resolved = [];
    protected array $alias    = [];

    protected Dominator $dominator;

    protected function build(string $abstract): mixed
    {

        try {
            $reflector = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']不存在:' . $e->getMessage());
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $abstract;
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());

        try {
            $instance = $reflector->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']实例化失败:' . $e->getMessage());
        }

        return $this->resolved[$reflector->getName()] = $instance;
    }

    private function getAlias(string $abstract): string
    {
        return isset($this->alias[$abstract]) ? $this->getAlias($this->alias[$abstract]) : $abstract;
    }

    private function resolveDependencies(array $parameters): array
    {
        return array_reduce($parameters, function (array $dependencies, ReflectionParameter $parameter) {
            if (!is_null($parameter->getType())) {
                $dependencies[] = $this->build($parameter->getType()->getName());
            }

            return $dependencies;
        }, []);
    }

    final protected function splice(string $haystack, string|array $needle): string
    {
        if (is_string($needle)) {
            return str_ends_with($haystack, $needle) ? $this->substr($haystack, $needle) : $haystack;
        }

        if (is_array($needle)) {
            $end = array_filter($needle, fn(string $ends) => str_ends_with($haystack, $ends));

            $end = array_pop($end);

            return $this->substr($haystack, $end);
        }

        return $haystack;
    }

    private function substr(string $haystack, string $needle): string
    {
        return substr($haystack, 0, -strlen($needle));
    }

    final protected function explode(): array
    {
        $splice = explode('\\', $this->dominator->getAlias());

        return ['namespace' => array_shift($splice), 'class' => array_pop($splice)];
    }

    abstract protected function resolve(string $abstract): mixed;

    abstract public function getModel(string $abstract = null): mixed;
}