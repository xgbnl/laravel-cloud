<?php

namespace Xgbnl\Cloud\Kernel\Proxy;

use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Exporter\Exporter;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Providers\EloquentBuilder;
use Xgbnl\Cloud\Providers\Model;

final class ServiceProxy extends QueryBuilderProxy implements Factory
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