<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Contacts\Exporter;
use Xgbnl\Cloud\Kernel\Providers\Contacts\Factory;
use Xgbnl\Cloud\Providers\EloquentBuilder;
use Xgbnl\Cloud\Providers\Model;

final class ServiceProvider extends QueryBuilderProvider implements Factory
{
    public function get(Contextual $contextual, string $name): Exporter|Model|EloquentBuilder|string
    {
        return match ($name) {
            'exporter' => $this->getConcrete($contextual->getAlias(), $name, ['service' => $contextual]),
            default    => parent::getConcrete($contextual->getAlias(), $name)
        };
    }

    public function getModel(string $abstract, string $final): string
    {
        if ($this->has()) {
            return $this->model;
        }

        $concrete = $this->splice($abstract, 'Exporters', $final);

        if (!class_exists($concrete)) {
            $this->modelNotExistsFail($concrete);
        }

        return $this->refresh($concrete);
    }

    protected function registerAccessor(): array|string
    {
        return 'Service';
    }

    protected function getConcreteParentAccessor(): string
    {
        return Exporter::class;
    }
}