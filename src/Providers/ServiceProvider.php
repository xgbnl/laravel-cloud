<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Exporter;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;

final class ServiceProvider extends QueryBuilderProvider implements Factory
{
    public function make(string $abstract): Exporter|Model|EloquentBuilder|string
    {
        return match ($abstract) {
            'exporter' => $this->resolve($abstract, ['service' => $this->dominator]),
            default    => self::make($abstract)
        };
    }

    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        $class = $this->resolveClass($abstract);

        if (!is_subclass_of($class, Exporter::class)) {
            throw new FailedResolveException('导出类[' . $class . ']未实现接口[' . Exporter::class . ']');
        }

        return $this->build($class, $parameters);
    }

    public function resolveClass(string $abstract = null): string
    {
        if ($this->dominator->getModelName()) {
            return $this->dominator->getModelName();
        }

        ['namespace' => $ns, 'class' => $class] = $this->explode();

        $class = $this->splice($class, 'Service');

        $class = $ns . '\\Exporters\\' . $class . ucwords($abstract);

        if (!class_exists($class)) {
            throw new FailedResolveException('调用导出方法失败,类[' . $class . ']未定义');
        }

        return $this->dominator->assign($class);
    }
}