<?php

namespace Xgbnl\Cloud\Repositories\Providers;

use Xgbnl\Cloud\Repositories\Contacts\RelationContact;

final class Column implements RelationContact
{
    protected string|array $columns = [];

    public function splitColumn(mixed $columns): array
    {
        return is_string($columns) ? explode(',', $columns) : $columns;
    }

    public function store(mixed $data): void
    {
        $this->columns = $this->splitColumn($data);
    }

    public function resources(): array
    {
        return $this->columns;
    }
}