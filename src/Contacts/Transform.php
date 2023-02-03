<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Contacts;

use Illuminate\Database\Eloquent\Model;

interface Transform
{
    public function transformers(Model $model): array;
}