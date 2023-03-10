<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Contracts\Database\Query\Builder as RawQuery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Contacts\Transform\Transform;
use Xgbnl\Cloud\Kernel\Providers\RepositoryProvider;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Accessor;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Query;
use Xgbnl\Cloud\Repositories\Supports\Querier;
use Xgbnl\Cloud\Repositories\Supports\Resources\Scope;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read RawQuery $rawQuery
 * @property-read Transform|null $transform
 * @method self select(string|array $columns)
 * @method self only(array $columns)
 * @method self except(array $columns)
 * @method self relation(array $with)
 * @method self query(array $params)
 * @method self transform(?string $call = null)
 * @method self chunk(int $count)
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children')
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false)
 */
abstract class Repositories implements Contextual, Query
{
    use ContextualTrait;

    private readonly Factory $factory;

    private readonly Accessor $scope;

    private readonly Query $querier;

    final public function __construct(RepositoryProvider $provider, Scope $scope, Querier $querier)
    {
        $this->factory = $provider;
        $this->scope   = $scope;

        $this->querier = $querier;
    }

    public function values(): array
    {
        return $this->querier->values();
    }

    public function value(): array
    {
        return $this->querier->value();
    }

    public function __call(string $name, array $arguments)
    {
        if ($this->scope->includes($name)) {
            $this->scope->store($name, $arguments);
            return $this;
        }

        return $this->factory->callAction($this, $name, $arguments);
    }
}