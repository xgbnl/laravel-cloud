<?php

namespace Xgbnl\Cloud\Repositories\Supports\Contacts;

interface Resolver extends Resourceful
{
    public function split(string|array $values): array;

    public function getResolved(): array;

    public function getValue(): mixed;
}