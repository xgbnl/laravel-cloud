<?php

namespace Xgbnl\Cloud\Services\Process\fillable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommonFillable extends Fillable
{

    protected function update(array $attributes, array $data, Builder $builder): Builder|Model
    {
        try {
            $this->model = $builder->updateOrCreate($attributes, $data);
        } catch (Exception $e) {
            $this->fillFailException();
        }

        return $this->model;
    }

    protected function create(array $data, Builder $builder): Builder|Model
    {
        try {
            $this->model = $builder->create($data);
        } catch (Exception $e) {
            $this->fillFailException();
        }

        return $this->model;
    }
}