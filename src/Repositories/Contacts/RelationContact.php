<?php

namespace Xgbnl\Cloud\Repositories\Contacts;

interface RelationContact
{
    public function store(mixed $data): void;

    public function resources(): mixed;
}