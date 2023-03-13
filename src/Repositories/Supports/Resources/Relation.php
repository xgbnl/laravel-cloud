<?php

namespace Xgbnl\Cloud\Repositories\Supports\Resources;

use Xgbnl\Cloud\Repositories\Supports\Contacts\Resolver;

final class Relation implements Resolver
{
    protected Resolver $resolver;

    protected array $relations = [];

    public function __construct(Selector $resolver)
    {
        $this->resolver = $resolver;
    }

    public function split(string|array $values): array
    {
        $model = array_combine(['model', 'columns'], explode(':', $values));

        $model['columns'] = $this->resolver->split($model['columns']);

        return $model;
    }

    /**
     * Get resolve model relation objects.
     * @return array<object>
     */
    public function getResolved(): array
    {
        return array_reduce($this->getValue(), function (array $collections, string $relation) {
            $collections[] = (object)$this->split($relation);
            return $collections;
        }, []);
    }

    public function getValue(): array
    {
        return $this->relations;
    }

    public function store(mixed $values): void
    {
        $this->relations = $values;
    }
}