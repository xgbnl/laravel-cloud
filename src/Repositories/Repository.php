<?php

namespace Xgbnl\Cloud\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Xgbnl\Cloud\Kernel\Providers\RepositoryProvider;
use Xgbnl\Cloud\Repositories\Providers\Column;

abstract class Repository extends Repositories
{
    final public function find(
        mixed        $value,
        array|string $columns = ['*'],
        string       $by = 'id',
        mixed        $with = [],
        bool         $transform = false,
        ?string      $replaceCall = null,
    ): array|Model|null
    {
        $builder = $this->loadWith($with);

        $model = $by === 'id'
            ? $builder->find($value, $this->columns($columns))
            : $builder->select($this->columns($columns))->where($by, $value)->first();

        if (is_null($model)) {
            return null;
        }

        if ($this->transform && $transform) {
            return is_null($replaceCall)
                ? $this->transform->transformers($model) :
                $this->transform->{$replaceCall}($model);
        }

        return $model;
    }

    final public function values(
        array|string $columns = ['*'],
        array        $params = [],
        mixed        $with = null,
        bool         $transform = false,
        bool         $chunk = false,
        int          $count = 200,
        ?string      $replaceCall = null,
    ): array
    {
        $builder = $this->loadWith($with);

        $columns = $this->columns($columns);

        $params = array_filter($params, fn(mixed $value) => !empty($value));

        if (!empty($params)) {
            $builder = $this->query($params, $builder);
        }

        if ($chunk) {
            return $this->chunk($columns, $count, $builder, $transform, $replaceCall);
        }

        if ($this->transform && $transform) {
            $list = [];

            $builder->select($columns)->each(function (Model $model) use ($replaceCall, &$list) {
                $list[] = is_null($replaceCall)
                    ? $this->transform->transformers($model)
                    : $this->transform->{$replaceCall}($model);
            });

            return $list;
        }

        return $builder->select($columns)->get()->toArray();
    }

    private function loadWith(mixed $with): Builder
    {
        $query = $this->query->clone();

        if ((is_array($with) && !empty($with)) || is_string($with)) {
            return $query->with($with);
        }

        return $query;
    }

    private function chunk(array $columns, int $count, Builder $builder, bool $transform, ?string $replaceCall): array
    {
        if ($builder->count() <= 0) {
            return [];
        }

        $list = [];
        $builder->select($columns)->chunkById($count, function (Collection $collection) use (&$list, $transform, $replaceCall) {
            $collection->each(function (Model $model) use (&$list, $transform, $replaceCall) {

                $list [] = (!is_null($this->transform) && $transform)
                    ? (is_null($replaceCall)
                        ? $this->transform->transformers($model)
                        : $this->transform->{$replaceCall}($model))
                    : $model;
            });
        });

        return $list;
    }

    final protected function query(array $params, Builder $builder): Builder
    {
        if (count($params) === 1) {
            $keys = array_keys($params);
            $column = array_pop($keys);

            return (isset($this->rules[$column]) && (is_string($this->rules[$column]) && !empty($this->rules[$column])))
                ? $this->matchQuery($column, $params[$column], $this->rules[$column], $builder)
                : $builder->where($params);
        }

        array_filter($params, function ($queryValue, $column) use (&$builder) {

            if (isset($this->rules[$column]) && !is_null($queryValue)) {
                $builder = $this->matchQuery($column, $queryValue, $this->rules[$column], $builder);
            } else {
                $builder = $builder->where($column, $queryValue);
            }
        }, ARRAY_FILTER_USE_BOTH);

        return $builder;
    }

    private function matchQuery(string $column, mixed $value, string $rule, Builder $builder): Builder
    {
        return match ($rule) {
            'like'  => $builder->where($column, $rule, '%' . $value . '%'),
            'date'  => is_array($value)
                ? $this->dateRange($column, $value, $builder)
                : $builder->whereDate($column, '>=', $value)->orWhereDate($column, '<=', $value),
            'in'    => $builder->whereIn($column, $value),
            'notin' => $builder->whereNotIn($column, $value),
        };
    }

    private function dateRange(string $column, array $values, Builder $builder): Builder
    {
        [$start, $end] = $values;

        return $builder->whereDate($column, '>=', $start)->orWhereDate($column, '<=', $end);
    }

    final protected function prepareJoinQuery(JoinClause $joinClause, string $first, ?string $operator = null, ?string $second = null, array|string $columns = ['*'])
    {
        if (is_null($second)) {
            [$second, $operator] = [$operator, '='];
        }

        return $joinClause->select(is_array($columns) ? $columns : $this->columns($columns))
            ->on($first, $operator, $second);
    }

    final protected function columns(string|array $columns): array
    {
        return is_string($columns) ? $this->slice($columns) : $columns;
    }

    final protected function slice(string $haystack, string $needle = ',', array $keys = []): array
    {
        $slice = explode($needle, $haystack);
        return empty($keys) ? $slice : array_combine($keys, $slice);
    }
}
