<?php

namespace Xgbnl\Cloud\Providers;

use ReflectionClass;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Properties;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;

readonly abstract class Provider
{
    protected Properties $current;

    public function __construct(Properties $current)
    {
        $this->current = $current;
    }

    final public static function bind(Properties $properties): Factory
    {
        return match (true) {
            self::endWith($properties, 'Controller') => new ControllerProvider($properties),
            self::endWith($properties, 'Repository') => new RepositoryProvider($properties),
            self::endWith($properties, 'Service')    => new QueryBuilderProvider($properties),
        };
    }

    private static function endWith(Properties $haystack, string $needle): bool
    {
        return str_ends_with(get_class($haystack), $needle);
    }

    protected function build(string $abstract, array $parameters = []): mixed
    {
        try {
            $refClass = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']不存在:' . $e->getMessage());
        }

        if (!$refClass->isInstantiable()) {
            throw new FailedResolveException('目标类[' . $abstract . ']无法被实例化');
        }

        try {
            $instance = empty($parameters) ? $refClass->newInstance() : $refClass->newInstance(...$parameters);
        } catch (ReflectionException $e) {
            throw new FailedResolveException('目标类[' . $abstract . ']实例化失败:' . $e->getMessage());
        }

        return $instance;
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
        $splice = explode('\\', $this->current->getCalledClass());

        return ['namespace' => array_shift($splice), 'class' => array_pop($splice)];
    }

    abstract protected function resolve(string $abstract, array $parameters = []): mixed;

    abstract public function resolveClass(string $abstract = null): mixed;

    abstract protected function failedResolved(): void;
}