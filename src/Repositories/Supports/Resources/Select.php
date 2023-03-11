<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources;

use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;

class Select implements Resolver
{
    protected array $columns = ['*'];

    public function split(string|array $values): array
    {
        return is_string($values) ? explode(',', $values) : $values;
    }

    public function getResolved(): array
    {
        return $this->columns;
    }

    public function getValue(): string|array
    {
        return $this->columns;
    }

    public function store(mixed $values): void
    {
        $this->columns = $this->split($values);
    }
}