<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources\OutPutter;

use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Accessor;
use Xgbnl\Cloud\Repositories\Supports\Contacts\OutPutter;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;
use Xgbnl\Cloud\Repositories\Supports\Resources\Filter;
use Xgbnl\Cloud\Repositories\Supports\Resources\Relation;
use Xgbnl\Cloud\Repositories\Supports\Resources\Selector;

final class Scope implements Accessor
{
    protected readonly Resolver $select;
    protected readonly Resolver $relation;
    protected readonly Resolver $filter;

    protected readonly OutPutter $transform;
    protected readonly OutPutter $chunk;

    protected Repositories $repositories;

    public function __construct(Selector $select, Relation $relation, Filter $filter, Transform $transform, Chunkable $chunk)
    {
        $this->relation = $relation;
        $this->select   = $select;
        $this->filter   = $filter;

        $this->chunk     = $chunk;
        $this->transform = $transform;
    }

    public function includes(string $value): bool
    {
        return in_array($value, ['select', 'transform', 'chunk', 'query', 'relation']);
    }

    public function inject(Repositories $repositories): self
    {
        $this->repositories = $repositories;
        return $this;
    }

    public function store(string $name, mixed $values): void
    {
        switch ($name) {
            case 'select':
                if (count($values) > 1) {
                    $this->select->store($values);
                } else {
                    $this->select->store(...$values);
                }
                break;
            case 'relation':
                $this->relation->store($values);
                break;
            case 'only':
            case 'except':
                $this->filter->store($values, $name);
                break;
            case 'transform':
            case 'chunk':
                $this->{$name}->store(...$values);
                break;
        }
    }
}