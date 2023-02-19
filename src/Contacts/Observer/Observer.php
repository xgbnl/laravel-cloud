<?php

namespace Xgbnl\Cloud\Contacts\Observer;

use Illuminate\Database\Eloquent\Model;

interface Observer
{
    public function created(Model $model): void;

    public function updated(Model $model): void;

    public function deleted(Model $model): void;
}