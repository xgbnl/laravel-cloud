<?php

namespace Xgbnl\Cloud\Kernel;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Xgbnl\Cloud\Exceptions\FailedResolveException;

final class Application
{

    protected array $resolved = [];

    protected array        $instances = [];
    protected static ?self $instance  = null;

    private function __construct()
    {
    }

    private function __clone(): void
    {
    }

    /**
     * @throws ReflectionException
     */
    public function make(string $abstract, array $parameters = []): object
    {

        $concrete = $this->getConcrete($abstract);

        if (!is_null($concrete)) {
            return $concrete;
        }

        return $this->build($abstract, $parameters);
    }

    /**
     * @throws ReflectionException
     */
    public function build(string $abstract, array $parameters = []): object
    {
        try {
            $reflector = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw  new FailedResolveException('Target class[' . $abstract . ']not exists.[' . $e->getMessage() . ']');
        }

        if (!empty($parameters)) {
            return $reflector->newInstance(...$parameters);
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $abstract();
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters());

        try {
            $instance = $reflector->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('Create new class fail.[' . $e->getMessage() . ']');
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveDependencies(array $parameters): array
    {
        return array_reduce($parameters, function (array $dependencies, ReflectionParameter $parameter) {

            if (!$parameter->hasType()) {
                $dependencies[] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
            }

            if ($parameter->hasType()) {

                if ($parameter->getType()->isBuiltin()) {
                    $dependencies[] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
                } else {
                    $instance = $parameter->isDefaultValueAvailable()
                        ? $this->make($parameter->getType()->getName(), $parameter->getDefaultValue())
                        : $this->make($parameter->getType()->getName());

                    $dependencies [] = $instance;

                    $this->store($parameter->getType()->getName(), $instance);
                }
            }

            return $dependencies;
        }, []);
    }

    protected function getConcrete(string $abstract): ?object
    {
        return $this->resolved[$abstract] ?? null;
    }

    protected function store(string $abstract, mixed $concrete): void
    {
        if (!array_key_exists($abstract, $this->resolved)) {
            $this->resolved[$abstract] = $concrete;
        }
    }

    public function singleton(string $abstract, Closure $closure): void
    {
        $this->instances[$abstract] = $closure;
    }

    public function getSingleton(string $abstract): Closure
    {
        return $this->instances[$abstract];
    }

    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}