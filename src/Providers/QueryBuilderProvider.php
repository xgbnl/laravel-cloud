<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;

class QueryBuilderProvider extends Provider implements Factory
{
    public function resolve(string $abstract): mixed
    {
        return match ($abstract) {
            'table' => $this->make($abstract)->getTable(),
            'model' => $this->make($abstract),
            'query' => $this->resolveClass()::query(),
        };
    }

    protected function make(string $abstract): mixed
    {
        $class = $this->resolveClass();

        return $this->build($class);
    }

    public function resolveClass(string $abstract = null): mixed
    {
        if (!$this->dominator->has() && $this->isSubclass()) {
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

    private function isSubclass(): bool
    {
        return is_subclass_of($this->dominator, Service::class) || is_subclass_of($this->dominator, Repositories::class);
    }

    protected function failedResolved(string $class = null, bool $exists = false): void
    {
        throw new FailedResolveException($exists ? '缺少模型[ ' . $class . ' ]' : '模型文件[' . $class . '必须继承[' . Model::class . ']');
    }
}