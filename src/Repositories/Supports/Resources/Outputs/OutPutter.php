<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources\Outputs;

use Xgbnl\Cloud\Repositories\Supports\Contacts\Resourceful;

abstract class OutPutter implements Resourceful
{
    protected bool $trigger = false;

    protected mixed $value = null;

    public function store(mixed $values): void
    {
        $this->trigger = true;

        $this->doStore($values);
    }

    abstract protected function doStore(string|int $value): void;

    abstract protected function getValue(): mixed;
}