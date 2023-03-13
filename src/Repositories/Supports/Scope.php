<?php

namespace Xgbnl\Cloud\Repositories\Supports;

use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Accessor;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;
use Xgbnl\Cloud\Repositories\Supports\Resources\Filter;
use Xgbnl\Cloud\Repositories\Supports\Resources\Relation;
use Xgbnl\Cloud\Repositories\Supports\Resources\Select;

final class Scope implements Accessor
{
    protected readonly Resolver $select;
    protected readonly Resolver $relation;

    protected readonly Resolver $filter;

    protected Repositories $repositories;

    protected array $outPutter = [];

    public function __construct(Select $select, Relation $relation, Filter $filter)
    {
        $this->relation = $relation;
        $this->select = $select;
        $this->filter = $filter;
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

        }
    }
}