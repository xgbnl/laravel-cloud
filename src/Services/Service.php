<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Services;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Exporter\Exporter;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Contacts\Services\FillContact;
use Xgbnl\Cloud\Kernel\Providers\ServiceProvider;
use Xgbnl\Cloud\Services\Process\Deleter\Deleter;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read Exporter $exporter
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false)
 */
abstract class Service implements Contextual
{
    use ContextualTrait;

    private readonly Factory     $factory;
    private readonly FillContact $common;
    private readonly FillContact $transactional;

    private readonly Deleter $deleter;

    final public function __construct(ServiceProvider $provider, FillContact $common, FillContact $transactional, Deleter $deleter)
    {
        $this->factory = $provider;
        $this->common = $common;
        $this->transactional = $transactional;
        $this->deleter = $deleter;
    }

    final public function createOrUpdate(array $data, string $by = 'id', bool $transaction = true): Model|EloquentBuilder
    {
        return $transaction
            ? $this->transactional->createOrUpdate($data, $by, $this->query)
            : $this->common->createOrUpdate($data, $by, $this->query);
    }

    final public function destroy(mixed $value, string $by = 'id'): int|bool
    {
        return is_array($value)
            ? $this->deleter->batch($value, $by, $this->query)
            : $this->deleter->single($value, $by, $this->query);
    }

    final public function export(): void
    {
        $this->exporter->export();
    }

    public function __call(string $name, array $arguments)
    {
        return $this->factory->callAction($this, $name, ... $arguments);
    }
}