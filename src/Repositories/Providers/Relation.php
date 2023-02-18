<?php

namespace Xgbnl\Cloud\Repositories\Providers;

use stdClass;
use Xgbnl\Cloud\Repositories\Contacts\RelationContact;

final class Relation implements RelationContact
{
    protected array $store = [];

    protected readonly Column $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function store(mixed $data): void
    {
        $this->store = $data;
    }

    /**
     * @return array<stdClass>
     */
    public function resources(): array
    {
        return array_reduce($this->store, function (array $resolved, string $relation) {
            $resolved[] = $this->resolve($relation);

            return $resolved;
        }, []);
    }

    public function resolve(string $relation): object
    {
        [$model, $columns] = explode(':', $relation);

        $columns = $this->column->splitColumn($columns);

        return new class ($model, $columns) {
            public function __construct(
                public readonly string $model,
                public readonly array  $columns,
            )
            {
            }
        };
    }
}