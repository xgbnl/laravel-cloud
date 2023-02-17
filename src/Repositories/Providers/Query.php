<?php

namespace Xgbnl\Cloud\Repositories\Providers;

final readonly class Query
{
    protected Column $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }


}