<?php

namespace Xgbnl\Cloud\Repositories\Factories;

use Xgbnl\Cloud\Contacts\Eloquent\Eloquent;

readonly class Query implements Eloquent
{
    protected Scope $scope;

    public function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    public function values(): mixed
    {
        // TODO: Implement values() method.
    }

    public function value(): mixed
    {
        // TODO: Implement value() method.
    }
}