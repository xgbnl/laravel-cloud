<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as RawBuilder;
use ReflectionException;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Contacts\Transform\Transform;
use Xgbnl\Cloud\Support\Str;

final  class RepositoryProvider extends Provider implements Factory
{
    protected QueryBuilderProvider $builderProxy;

    public function __construct(Str $str, QueryBuilderProvider $builderProxy)
    {
        $this->builderProxy = $builderProxy;
        parent::__construct($str);
    }

    /**
     * @throws ReflectionException
     */
    public function get(Contextual $contextual, string $name): RawBuilder|EloquentBuilder|Transform|Model|string|null
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

        $concrete = $this->splice($abstract, 'Transforms');
        $concrete = $concrete . ucwords($final);

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