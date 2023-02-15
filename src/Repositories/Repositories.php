<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Dominator;
use Xgbnl\Cloud\Providers\Provider;
use Xgbnl\Cloud\Providers\RepositoryProvider;
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
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children') 为列表生成树结构
 */
abstract class Repositories implements Dominator
{
    use CallMethodCollection, PropertiesTrait;

    private readonly Factory $factory;

    public function __construct(RepositoryProvider $provider)
    {
        $this->factory = $provider;
    }
}
