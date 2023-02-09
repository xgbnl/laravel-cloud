<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Exporter;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Properties;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Services\Service;

final readonly class ServiceProvider extends Provider implements Factory
{
    protected QueryBuilderProvider|Factory $queryBuilderProvider;

    protected Service|Properties $properties;

    public function __construct(Properties $dominator)
    {
        $this->properties = $dominator;

        $this->queryBuilderProvider = new QueryBuilderProvider($dominator);

        parent::__construct($dominator);
    }

    public function make(string $abstract): mixed
    {
        return match ($abstract) {
            'exporter' => $this->resolve($abstract, ['service' => $this->dominator]),
            default    => $this->queryBuilderProvider->make($abstract)
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
        if ($this->properties->getModelName()) {
            return $this->properties->getModelName();
        }

        ['namespace' => $ns, 'class' => $class] = $this->explode();

        $class = $this->splice($class, 'Service');

        $class = $ns . '\\Exporters\\' . $class . ucwords($abstract);

        if (!class_exists($class)) {
            $this->failedResolved($class);
        }

        return $this->dominator->assign($class);
    }

    protected function failedResolved(string $class = null): void
    {
        throw new FailedResolveException('调用导出方法失败,类[' . $class . ']未定义');
    }
}