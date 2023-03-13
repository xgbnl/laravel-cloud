<?php

namespace Xgbnl\Cloud\Repositories\Supports;

use Illuminate\Database\Eloquent\Builder;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Accessor;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Query as QueryContact;
use Xgbnl\Cloud\Repositories\Supports\Resources\Scope;

class Querier implements QueryContact
{
    protected readonly Accessor $scope;

    protected ?Builder $builder = null;

    public function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    public function values(): array
    {
        // TODO: Implement values() method.
    }

    public function value(): array
    {
        // TODO: Implement value() method.
    }
}