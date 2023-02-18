<?php

namespace Xgbnl\Cloud\Repositories\Providers;

use Xgbnl\Cloud\Repositories\Contacts\RelationContact;

class Transform implements RelationContact
{
    protected bool $transform = false;

    protected ?string $call = null;

    public function store(mixed $data = null): void
    {
        $this->call = $data;

        $this->transform = true;
    }

    public function resources(): ?string
    {
        return $this->call;
    }
}