<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources;

use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;
use Xgbnl\Cloud\Repositories\Supports\Contacts\Resourceful;

class Filter implements Resolver
{
    protected array $filters = [];

    protected ?string $option = null;

    public function store(mixed $values, ?string $option = null): void
    {
        $this->filters = $values;
        $this->option = $option;
    }

    public function split(array|string $values): array
    {
        return [];
    }

    public function getResolved(): array
    {
        return $this->filters;
    }

    public function getValue(): string
    {
        return $this->option;
    }
}