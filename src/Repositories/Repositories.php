<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Repositories;

use Xgbnl\Cloud\Contacts\Contextual;
use Xgbnl\Cloud\Contacts\Transform;
use Xgbnl\Cloud\Kernel\Proxies\Contacts\Factory;
use Xgbnl\Cloud\Kernel\Proxies\RepositoryProxy;
use Xgbnl\Cloud\Proxy\Proxies;
use Xgbnl\Cloud\Repositories\Providers\Eloquent;
use Xgbnl\Cloud\Traits\ContextualTrait;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read QueryBuilder $rawQuery
 * @property-read Transform|null $transform
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children')
 * @method self select(string|array $columns);
 * @method self relation(array $with);
 * @method self transform(string $call = null);
 */
abstract class Repositories implements Contextual
{
    use ContextualTrait;

    private readonly Factory $factory;

    protected readonly Eloquent $eloquent;

    private readonly Proxies $proxies;

    protected array $rules = [];

    final public function __construct(RepositoryProxy $provider, Eloquent $eloquent)
    {
        $this->factory = $provider;
        $this->eloquent = $eloquent;
    }

    public function __call(string $method, array $parameters)
    {
        if (property_exists($this->eloquent, $method) && method_exists($this->eloquent->{$method}, $method)) {
            $this->eloquent->{$method}(...$parameters);
            return $this;
        }
    }
}
