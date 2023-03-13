<?php

namespace Xgbnl\Cloud\Repositories\Supports\Contacts;

interface Query
{
    public function values(): array;

    public function value(): array;
}