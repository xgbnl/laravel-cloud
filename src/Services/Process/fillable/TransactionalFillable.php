<?php

namespace Xgbnl\Cloud\Services\Process\fillable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransactionalFillable extends Fillable
{

    protected function update(array $attributes, array $data, Builder $builder): Builder|Model
    {
        try {
            DB::beginTransaction();
            $this->model = $builder->updateOrCreate($attributes, $data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->fillFailException($e);
        }

        return $this->model;
    }

    protected function create(array $data, Builder $builder): Builder|Model
    {
        try {
            DB::beginTransaction();
            $this->model = $builder->create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->fillFailException($e);
        }

        return $this->model;
    }
}