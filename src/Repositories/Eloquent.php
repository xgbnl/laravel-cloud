<?php

namespace Xgbnl\Cloud\Repositories;

use Xgbnl\Cloud\Exceptions\FailedResolveException;

final class Eloquent
{
    protected mixed $select = ['*'];

    protected array $relation = [];

    protected array $query = [];

    protected bool $chunk = false;
    protected int  $count = 200;

    protected bool    $transform = false;
    protected ?string $call      = null;

    public function store(string $name, mixed $value): void
    {
        switch ($name) {
            case 'select':
                $this->select = $this->splitColumn($value);
                break;
            case 'relation':
                $this->relation = $value;
                break;
            case 'query':
                $this->query = $value;
                break;
            case 'chunk':
                $this->chunk = true;
                $this->count = $value;
                break;
            case 'transform':
                $this->transform = true;
                $this->call      = $value;
                break;
            default:
                throw new FailedResolveException('Call undefined method [' . $name . ']');
        }
    }

    public function relationIsEmpty(): bool
    {
        return empty($this->relation);
    }

    public function getResolveRelationship(): array
    {
        return array_reduce($this->relation, function (array $resolved, string $relation) {
            $resolved[] = $this->resolve($relation);
            return $resolved;
        }, []);
    }

    protected function resolve(string $relation): object
    {
        $model = (object)array_combine(['model', 'columns'], explode(':', $relation));

        $model->columns = $this->splitColumn($model->columns);

        return $model;
    }

    protected function splitColumn(mixed $columns): array
    {
        return is_string($columns) ? explode(',', $columns) : $columns;
    }
}