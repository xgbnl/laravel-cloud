<?php

namespace Xgbnl\Cloud\Repositories\Supports\Contacts;

interface Accessor
{
    public function includes(string $value): bool;
}