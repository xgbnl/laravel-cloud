<?php

namespace Xgbnl\Cloud\Contacts\Services;

use Illuminate\Database\Eloquent\Builder;

interface FillContact
{
    public function createOrUpdate(mixed $data, string $by, Builder $builder): mixed;
}