<?php

namespace Xgbnl\Cloud\Repositories\Providers;

use Xgbnl\Cloud\Repositories\Contacts\RelationContact;

final readonly class Eloquent
{
    public RelationContact $relation;

    public RelationContact $select;

    public RelationContact $transform;

    public function __construct(Relation $relation, Column $column, Transform $transform)
    {
        $this->relation = $relation;
        $this->select = $column;
        $this->transform = $transform;
    }
}