<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final readonly class QueryBuilderProvider extends Provider implements Factory
{

    public function make(string $abstract): Model|EloquentBuilder|string
    {
        return match ($abstract) {
            'table' => $this->resolve($abstract)->getTable(),
            'model' => $this->resolve($abstract),
            'query' => $this->resolveClass()::query(),
        };
    }

    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        $class = $this->resolveClass();

        return $this->build($class);
    }

    public function resolveClass(string $abstract = null): mixed
    {
        if (!$this->dominator->isNull()) {
            return $this->dominator->getModelName();
        }

        ['namespace' => $ns, 'class' => $baseName] = $this->explode();

        $baseName = $this->splice($baseName, ['Repository', 'Service']);

        $class = $ns . '\\Models\\' . $baseName;

        if (!class_exists($class)) {
            $this->failedResolved($class, true);
        }

        if (!is_subclass_of($class, Model::class)) {
            $this->failedResolved($class);
        }

        return $this->dominator->assign($class);
    }

    protected function failedResolved(string $class = null, bool $exists = false): void
    {
        throw new FailedResolveException($exists ? '缺少模型[ ' . $class . ' ]' : '模型文件[' . $class . '必须继承[' . Model::class . ']');
    }
}