<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Contracts\Database\Query\Builder as RawQuery;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Factories\Access;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Contacts\Transform\Transform;
use Xgbnl\Cloud\Kernel\Providers\RepositoryProvider;
use Xgbnl\Cloud\Repositories\Factories\Query;
use Xgbnl\Cloud\Repositories\Factories\Scope;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read RawQuery $rawQuery
 * @property-read Transform|null $transform
 * @method self select(string|array $columns)
 * @method self except(string|array $columns)
 * @method self relation(array $with)
 * @method self query(array $params)
 * @method self transform(string $call = 'transform')
 * @method self chunk(int $count)
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children')
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false)
 */
abstract class Repositories implements Contextual
{
    use ContextualTrait;

    private readonly Factory $factory;

    private readonly Scope $scope;

    private readonly Access $eloquent;

    protected array $rules = [];

    final public function __construct(RepositoryProvider $provider, Scope $scope, Query $eloquent)
    {
        $this->factory = $provider;
        $this->scope = $scope;
        $this->eloquent = $eloquent;
    }

    public function __call(string $name, array $arguments)
    {
        if (property_exists($this->scope, $name)) {
            $this->scope->store($name, ...$arguments);
            return $this;
        }

        return $this->provider->callAction($this, $name, $arguments);
    }
}