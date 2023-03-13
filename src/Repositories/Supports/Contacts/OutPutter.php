<?php

namespace Xgbnl\Cloud\Repositories\Supports\Contacts;

interface OutPutter
{
    public function getValue(): mixed;

    public function trigger(): bool;

    public function store(mixed $value):void;
}