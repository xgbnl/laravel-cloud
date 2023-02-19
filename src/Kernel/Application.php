<?php

namespace Xgbnl\Cloud\Kernel;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Xgbnl\Cloud\Contacts\Contextual;
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
        // TODO: Implement __clone() method.
    }

    /**
     * @throws ReflectionException
     */
    public function make(string $abstract, array $parameters = []): mixed
    {

        return $this->build($abstract, $parameters);
    }

    /**
     * @throws ReflectionException
     */
    public function build(string $abstract, array $parameters = []): mixed
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
    private function resolveDependencies(array $dependencies): array
    {
        return array_reduce($dependencies, function (array $result, ReflectionParameter $parameter) {
            if (!is_null($parameter->getType())) {
                $result[] = $this->build($parameter->getType()->getName());
            }

            return $result;
        }, []);
    }

    public function singleton(string $abstract, Closure $closure): void
    {
        $this->instances[$abstract] = $closure;
    }

    protected function getConcrete(string $abstract): Closure
    {
        return $this->instances[$abstract];
    }

    public function callAction(Contextual $contextual, string $method, array $parameters)
    {
        if (method_exists($this->getConcrete('proxies')(), $method)) {
            return $this->getConcrete('proxies')()->{$method}(...$parameters);
        }

        throw new FailedResolveException('Method ' . $contextual->getAlias() . '::' . $method . 'does not exist.');
    }

    public static function getInstance(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}