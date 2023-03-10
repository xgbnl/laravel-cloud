<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Repositories\Repository;

final  class CacheProvider extends Provider implements Factory
{
    /**
     * @throws ReflectionException
     */
    public function get(Contextual $contextual, string $name): Repository
    {
        return match ($name) {
            'repository' => $this->getConcrete($contextual->getAlias(), $name),
        };
    }

    public function getModel(string $abstract, string $final): string
    {
        if ($this->has()) {
            return $this->model;
        }

        $concrete = $this->splice($abstract, 'Repositories');
        $concrete = $concrete . ucwords($final);

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