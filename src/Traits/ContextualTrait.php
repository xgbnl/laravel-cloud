<?php

namespace Xgbnl\Cloud\Traits;

trait ContextualTrait
{
    public function __get(string $name)
    {
        return $this->factory->get($name);
    }
}