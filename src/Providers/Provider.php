<?php

namespace Xgbnl\Cloud\Providers;

use ReflectionClass;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Dominator;
use Xgbnl\Cloud\Exceptions\FailedResolveException;

abstract class Provider
{
    protected Dominator $dominator;

    protected array $container = [];

    public function __construct(Dominator $dominator)
    {
        $this->dominator = $dominator;
    }

    /**
     * @throws ReflectionException
     */
    protected function build(string $abstract, array $parameters = []): mixed
    {
        try {
            $reflector = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']不存在:' . $e->getMessage());
        }

        if (!$reflector->isInstantiable()) {
            if (isset($this->container[$reflector->getName()])) {
                return $this->container[$reflector->getName()];
            }

            throw new FailedResolveException('目标类[' . $abstract . ']无法被实例化');
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $reflector->newInstance();
        }

        $dependencies = $this->factory($reflector->getConstructor()->getParameters());

        try {
            $instance = $reflector->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']实例化失败:' . $e->getMessage());
        }

        return $instance;
    }

    private function factory(array $parameters): array
    {
        return array_reduce($parameters, function (array $dependencies, \ReflectionParameter $parameter) {
            if (is_null($parameter->getType())) {
                $dependencies[] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : '0';
            } else {
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
        $splice = explode('\\', $this->dominator->getCalledClass());

        return ['namespace' => array_shift($splice), 'class' => array_pop($splice)];
    }

    abstract protected function resolve(string $abstract, array $parameters = []): mixed;

    abstract public function resolveClass(string $abstract = null): mixed;
}