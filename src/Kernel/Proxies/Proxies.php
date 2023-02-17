<?php

namespace Xgbnl\Cloud\Kernel\Proxies;

use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Kernel\Proxies\Contacts\Proxyable;
use Xgbnl\Cloud\Kernel\Str;

abstract class Proxies implements Proxyable
{
    protected ?string $model = null;

    protected Str $str;

    public function __construct(Str $str)
    {
        $this->str = $str;
    }

    final public function has(): bool
    {
        return !is_null($this->model);
    }

    final public function refresh(string $class): string
    {
        return $this->model = $class;
    }

    final public function getAccessor(): string
    {
        return $this->model;
    }

    final protected function getConcrete(string $abstract, string $final, array $parameters = []): mixed
    {
        $concrete = $this->getModel($abstract, $final);

        if (is_null($concrete)) {
            return $concrete;
        }

        if ($this->notInherited($concrete)) {
            $this->inheritedOrImplementFail($concrete);
        }

        return Application::getInstance()->make($concrete, $parameters);
    }

    final protected function split(string $haystack): string
    {
        return $this->str->split($haystack, $this->registerAccessor());
    }

    final protected function splice(string $abstract, string $namespace, ?string $suffix = null): string
    {
        [$ns, $concrete] = $this->str->explode($abstract);

        $class = $ns . '\\' . $namespace . '\\' . $this->split($concrete);

        return $suffix ? $class : $class . ucwords($suffix);
    }

    final protected function notInherited(string $concrete): bool
    {
        return !is_subclass_of($concrete, $this->getConcreteParentAccessor());
    }

    final protected function inheritedOrImplementFail(string $concrete): void
    {
        throw new FailedResolveException('The class file [' . $concrete . '] must be inherited or implement [' . $this->getConcreteParentAccessor() . ']');
    }

    final protected function modelNotExistsFail(string $concrete): void
    {
        throw new FailedResolveException('Missing class [' . $concrete . ']');
    }

    abstract public function getModel(string $abstract, string $final): mixed;

    abstract protected function registerAccessor(): array|string;

    abstract protected function getConcreteParentAccessor(): string;
}