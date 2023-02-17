<?php

namespace Xgbnl\Cloud\Traits;

trait ContextualTrait
{
    public function __get(string $name)
    {
        return $this->factory->get($this,$name);
    }

    public function getAlias(): string
    {
        return get_called_class();
    }
}