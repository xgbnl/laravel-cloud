<?php

namespace Xgbnl\Cloud\Traits;

trait DominatorTrait
{
    private ?string $class = null;

    public function __get(string $name)
    {
        return $this->factory->resolve($name);
    }

    public function getAlias(): string
    {
        return get_called_class();
    }

    public function has(): bool
    {
        return is_null($this->class);
    }

    public function assign(string $abstract): string
    {
        return $this->class = $abstract;
    }

    public function getModelName(): ?string
    {
        return $this->class;
    }
}