<?php

namespace Xgbnl\Cloud\Decorates\Contacts;

interface Decorate
{
    public function filter(array $origin, mixed $fields): array;

    public function arrayFields(array $origin, mixed $fields): array;
}