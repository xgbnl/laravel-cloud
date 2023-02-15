<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Transform;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Illuminate\Database\Query\Builder as RawBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final  class RepositoryProvider extends QueryBuilderProvider implements Factory
{
    public function resolve(string $abstract): RawBuilder|EloquentBuilder|Transform|string|null
    {
        return match ($abstract) {
            'transform' => $this->make($abstract),
            'rawQuery'  => parent::make('model')->newQuery(),
            default     => parent::make($abstract),
        };
    }

    protected function make(string $abstract): mixed
    {
        $class = $this->resolveClass($abstract);

        if (is_null($class)) {
            return null;
        }

        if (!is_subclass_of($class, Transform::class)) {
            throw new FailedResolveException('Transform模型[' . $class . ']错误,必须实现[' . Transform::class . ']接口');
        }

        return $this->build($class);
    }

    public function resolveClass(string $abstract = null): ?string
    {
        if ($this->dominator->isNull()) {
            return $this->dominator->getModelName();
        }

        ['namespace' => $ns, 'class' => $class] = $this->explode();

        $class = $this->splice($class, 'Repository');

        $class = $ns . '\\Transforms\\' . $class . ucwords($abstract);

        return !class_exists($class) ? null : $this->dominator->assign($class);
    }
}