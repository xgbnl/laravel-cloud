<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Contacts\Transform;
use Xgbnl\Cloud\Kernel\Proxies\Contacts\Factory;
use Xgbnl\Cloud\Kernel\Proxies\RepositoryProxy;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read QueryBuilder $rawQuery
 * @property-read Transform|null $transform
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children') 为列表生成树结构
 */
abstract class Repositories implements Contextual
{
    use CallMethodCollection, ContextualTrait;

    private readonly Factory $factory;

    public function __construct(RepositoryProxy $provider)
    {
        $this->factory = $provider;
    }
}
