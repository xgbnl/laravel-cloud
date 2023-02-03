<?php

namespace Xgbnl\Cloud\Attributes;

use Attribute;

#[Attribute]
readonly class OAName
{
    public array|string $businessModels;

    public function __construct(array|string $businessModels)
    {
        $this->businessModels = $businessModels;
    }
}