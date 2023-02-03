<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Properties;
use Xgbnl\Cloud\Contacts\Transform;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Illuminate\Database\Query\Builder as RawBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final readonly class RepositoryProvider extends Provider implements Factory
{
    protected readonly QueryBuilderProvider|Factory $queryBuilderProvider;

    public function __construct(Properties $current)
    {
        $this->queryBuilderProvider = new QueryBuilderProvider($current);

        parent::__construct($current);
    }

    public function make(string $abstract): RawBuilder|EloquentBuilder|Transform|null
    {
        return match ($abstract) {
            'rawQuery'  => $this->queryBuilderProvider->make('model')->newQuery(),
            'transform' => $this->resolve($abstract),
            default     => $this->queryBuilderProvider->make($abstract),
        };
    }

    protected function resolve(string $abstract, array $parameters = []): mixed
    {
        $class = $this->resolveClass($abstract);

        if (is_null($class)) {
            return null;
        }

        if (!is_subclass_of($class, Transform::class)) {
            $this->failedResolved($class);
        }

        return $this->build($class);
    }

    public function resolveClass(string $abstract = null): ?string
    {
        if ($this->dominator->isNull()) {
            return $this->dominator->getModelName();
        }

        ['namespace' => $ns, 'class' => $class] = $this->explode();

        $class = $ns . '\\Transforms\\' . $class . ucwords($abstract);

        if (!class_exists($class)) {
            return null;
        }

        return $this->dominator->assign($class);
    }

    protected function failedResolved(string $class = null, bool $exists = false): void
    {
        throw new FailedResolveException('Transform模型[' . $class . ']错误,必须实现[' . Transform::class . ']接口');
    }
}