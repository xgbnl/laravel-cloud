<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Contacts\Transform\Transform;
use Xgbnl\Cloud\Kernel\Proxy\RepositoryProxy;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read QueryBuilder $rawQuery
 * @property-read Transform|null $transform
 * @method self select(string|array $columns)
 * @method self relation(array $with)
 * @method self query(array $params)
 * @method self transform(string $call = null)
 * @method self chunk(int $count)
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children')
 * @method mixed endpoint(mixed $needle, string $domain, bool $replace = false)
 */
abstract class Repositories implements Contextual
{
    use ContextualTrait;

    private readonly Factory $factory;

    protected readonly Eloquent $eloquent;

    protected array $rules = [];

    final public function __construct(RepositoryProxy $proxy, Eloquent $eloquent)
    {
        $this->factory  = $proxy;
        $this->eloquent = $eloquent;
    }

    public function __call(string $method, array $parameters)
    {
        if (property_exists($this->eloquent, $method)) {
            $this->eloquent->store($method, ...$parameters);
            return $this;
        }

        return $this->factory->callAction($this, $method, $parameters);
    }
}