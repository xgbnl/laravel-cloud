<?php

namespace Xgbnl\Cloud\Kernel\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Exporter\Exporter;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Support\Str;

final class ServiceProvider extends Provider implements Factory
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