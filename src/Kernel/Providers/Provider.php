<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use BadMethodCallException;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Providers\Provided;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Support\Constant;
use Xgbnl\Cloud\Support\Str;

abstract class Provider implements Provided
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

    /**
     * @throws ReflectionException
     */
    final protected function getConcrete(string $abstract, string $final, array $parameters = []): mixed
    {
        $concrete = $this->getModel($abstract, $final);

        if (is_null($concrete)) {
            return null;
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

    final protected function splice(string $abstract, string $levelName, ?string $suffix = null): string
    {
        ['namespace' => $ns, 'class' => $concrete] = $this->str->explode($abstract);

        $class = $ns . '\\' . $levelName . '\\' . $this->split($concrete);

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

    final public function getConstant(): Constant
    {
        return $this->app->getSingleton('constant')();
    }

    final public function callAction(Contextual $contextual, string $name, array $arguments)
    {
        if (method_exists($this->getConstant(), $name)) {
            return $this->getConstant()->{$name}(...$arguments);
        }

        throw new BadMethodCallException('Method ' . $contextual->getAlias() . '::' . $name . 'does not exist.');
    }

    abstract public function getModel(string $abstract, string $final): mixed;

    abstract protected function registerAccessor(): array|string;

    abstract protected function getConcreteParentAccessor(): string;
}