<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Properties;
use Xgbnl\Cloud\Providers\Provider;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Contacts\Transform;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Xgbnl\Cloud\Traits\PropertiesTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read QueryBuilder $rawQuery
 * @property-read Transform|null $transform
 */
abstract class Repositories implements Properties
{
    use CallMethodCollection,PropertiesTrait;

    private readonly Factory $factory;

    public function __construct()
    {
        $this->factory = Provider::bind($this);
    }
}
