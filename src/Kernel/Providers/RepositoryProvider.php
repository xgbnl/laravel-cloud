<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as RawBuilder;
use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Contacts\Transform;
use Xgbnl\Cloud\Kernel\Providers\Contacts\Factory;

final  class RepositoryProvider extends QueryBuilderProvider implements Factory
{
    public function get(Contextual $contextual, string $name): RawBuilder|EloquentBuilder|Transform|string|null
    {
        return match ($name) {
            'transform' => $this->getConcrete($contextual->getAlias(), $name),
            'rawQuery'  => parent::getConcrete($contextual->getAlias(), 'model')->newQuery(),
            default     => parent::getConcrete($contextual->getAlias(), $name),
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