<?php

namespace Xgbnl\Cloud\Repositories\Supports;

use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Accessor;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Scoperty;
use Xgbnl\Cloud\Repositories\Supports\Resources\Relation;
use Xgbnl\Cloud\Repositories\Supports\Resources\Select;

final class Scope implements Accessor
{
    protected readonly Resolver $select;
    protected readonly Resolver $relation;

    protected Repositories $repositories;

    public function __construct(Select $select, Relation $relation)
    {
        $this->relation = $relation;
        $this->select = $select;
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

    }
}