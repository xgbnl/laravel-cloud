<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Illuminate\Database\Eloquent\Model;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Services\Service;

class QueryBuilderProvider extends Provider implements Factory
{
    /**
     * @throws ReflectionException
     */
    public function get(Contextual $contextual, string $name): mixed
    {
        return match ($name) {
            'table' => $this->getConcrete($contextual->getAlias(), $name)->getTable(),
            'model' => $this->getConcrete($contextual->getAlias(), $name),
            'query' => $this->getModel($contextual->getAlias(), $name)::query(),
        };
    }

    public function getModel(string $abstract, string $final): mixed
    {
        if ($this->has() && $this->subClassOf($abstract)) {
            return $this->model;
        }

        $contact = $this->splice($abstract, 'Models', $final);

        if (!class_exists($contact)) {
            $this->modelNotExistsFail($contact);
        }

        return $this->refresh($contact);
    }

    private function subClassOf(string $abstract): bool
    {
        return is_subclass_of($abstract, Service::class) || is_subclass_of($abstract, Repositories::class);
    }

    protected function registerAccessor(): array|string
    {
        return ['Repository', 'Service'];
    }

    protected function getConcreteParentAccessor(): string
    {
        return Model::class;
    }
}