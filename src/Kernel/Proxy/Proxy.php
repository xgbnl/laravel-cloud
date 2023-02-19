<?php

namespace Xgbnl\Cloud\Kernel\Proxy;

use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Proxyable;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Kernel\Proxies;
use Xgbnl\Cloud\Kernel\Str;

abstract class Proxy implements Proxyable
{
    protected ?string $model = null;

    protected Str $str;

    protected Application $app;

    public function __construct(Str $str)
    {
        $this->str = $str;
        $this->app = Application::getInstance();
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

    final public function proxy(): Proxies
    {
        return $this->app->getConcrete('proxies')();
    }

    final public function callAction(Contextual $contextual, string $method, array $parameters)
    {
        if (method_exists($this->proxy(), $method)) {
            return $this->proxy()->{$method}(...$parameters);
        }

        throw new FailedResolveException('Method ' . $contextual->getAlias() . '::' . $method . 'does not exist.');
    }

    abstract public function getModel(string $abstract, string $final): mixed;

    abstract protected function registerAccessor(): array|string;

    abstract protected function getConcreteParentAccessor(): string;
}