<?php

namespace Xgbnl\Cloud\Repositories\Factories;

use Xgbnl\Cloud\Contacts\Factories\Access;

readonly class Query implements Access
{
    protected Scope $scope;

    public function __construct(Scope $scope)
    {
        $this->scope = $scope;
    }

    public function values(): mixed
    {

    }

    public function value(): mixed
    {
        // TODO: Implement value() method.
    }
}