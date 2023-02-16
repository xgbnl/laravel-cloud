<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;

class QueryBuilderProvider extends Provider implements Factory
{

    public function get(string $abstract): mixed
    {
        return match ($abstract) {
            'table' => $this->resolve($abstract)->getTable(),
            'model' => $this->resolve($abstract),
            'query' => $this->getModel()::query(),
        };
    }

    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        $class = $this->getModel();

        return $this->factory($class);
    }

    public function getModel(string $abstract = null): mixed
    {
        if (!$this->dominator->has() && $this->isSubclass()) {
            return $this->dominator->getModelName();
        }

        ['namespace' => $ns, 'class' => $baseName] = $this->explode();

        $baseName = $this->splice($baseName, ['Repository', 'Service']);

        $class = $ns . '\\Models\\' . $baseName;

        if (!class_exists($class)) {
            throw new FailedResolveException('缺少模型[ ' . $class . ' ]');
        }

        if (!is_subclass_of($class, Model::class)) {
            throw new FailedResolveException('模型文件[' . $class . '必须继承[' . Model::class . ']');
        }

        return $this->dominator->assign($class);
    }

    private function isSubclass(): bool
    {
        return is_subclass_of($this->dominator, Service::class) || is_subclass_of($this->dominator, Repositories::class);
    }
}