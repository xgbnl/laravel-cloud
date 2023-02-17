<?php

namespace Xgbnl\Cloud\Repositories\Providers;

final readonly class Column
{
    public function resolve(mixed $columns):array
    {
        return is_string($columns) ? explode(',',$columns) : $columns;
    }
}