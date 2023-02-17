<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Kernel\Providers\Contacts\Factory;
use Xgbnl\Cloud\Repositories\Repository;

final  class CacheProvider extends Provider implements Factory
{
    public function get(Contextual $contextual, string $name): Repository
    {
        return match ($name) {
            'repository' => $this->getConcrete($contextual->getAlias(), $name),
        };
    }

    public function getModel(string $abstract, string $final): string
    {
        if (!$this->has()) {
            return $this->model;
        }

        $concrete = $this->splice($abstract, 'Repositories', $final);

        if (!class_exists($concrete)) {
            $this->modelNotExistsFail($concrete);
        }

        return $this->refresh($concrete);
    }

    protected function registerAccessor(): array|string
    {
        return 'Cache';
    }

    protected function getConcreteParentAccessor(): string
    {
        return Repository::class;
    }
}