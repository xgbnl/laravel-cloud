<?php

namespace Xgbnl\Cloud\Kernel\Proxy;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as RawBuilder;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Contacts\Transform\Transform;
use Xgbnl\Cloud\Kernel\Str;

final  class RepositoryProxy extends QueryBuilderProxy implements Factory
{
    protected QueryBuilderProxy $builderProxy;

    public function __construct(Str $str, QueryBuilderProxy $builderProxy)
    {
        $this->builderProxy = $builderProxy;
        parent::__construct($str);
    }

    public function get(Contextual $contextual, string $name): RawBuilder|EloquentBuilder|Transform|string|null
    {
        return match ($name) {
            'transform' => $this->getConcrete($contextual->getAlias(), $name),
            'rawQuery'  => $this->builderProxy->getConcrete($contextual->getAlias(), 'model')->newQuery(),
            default     => $this->builderProxy->get($contextual, $name),
        };
    }

    public function getModel(string $abstract, string $final): ?string
    {
        if ($this->has()) {
            return $this->model;
        }

        $concrete = $this->splice($abstract, 'Transforms', $final);

        return !class_exists($concrete) ? null : $this->refresh($concrete);
    }

    protected function registerAccessor(): array|string
    {
        return 'Repository';
    }

    protected function getConcreteParentAccessor(): string
    {
        return Transform::class;
    }
}