<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources\OutPutter;

use Xgbnl\Cloud\Repositories\Supports\Contacts\OutPutter as Contact;

abstract class OutPutter implements Contact
{
    protected bool $trigger = false;

    public function trigger(): bool
    {
        return $this->trigger;
    }

    public function store(mixed $value): void
    {
        $this->trigger = true;

        if (!is_null($value)) {
            $this->configure($value);
        }
    }

    abstract protected function configure(string|int $value): void;
}