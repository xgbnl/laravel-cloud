<?php

namespace Xgbnl\Cloud\Kernel\Proxy;

use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Exporter\Exporter;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Kernel\Str;
use Xgbnl\Cloud\Providers\EloquentBuilder;
use Xgbnl\Cloud\Providers\Model;

final class ServiceProxy extends Proxy implements Factory
{
    protected QueryBuilderProxy $builderProxy;

    public function __construct(Str $str, QueryBuilderProxy $builderProxy)
    {
        $this->builderProxy = $builderProxy;

        parent::__construct($str);
    }

    public function get(Contextual $contextual, string $name): Exporter|Model|EloquentBuilder|string
    {
        return match ($name) {
            'exporter' => $this->getConcrete($contextual->getAlias(), $name, ['service' => $contextual]),
            default    => $this->builderProxy->get($contextual, $name)
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