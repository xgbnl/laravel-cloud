<?php

namespace Xgbnl\Cloud\Services\Process\Deleter;

use HttpRuntimeException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final readonly class Deleter
{
    /**
     * @throws HttpRuntimeException
     */
    public function single(int|string $value, string $by, Builder $builder): bool
    {
        $query = $builder->where($by, $value);

        if (!(clone $query->exists())) {
            throw new HttpRuntimeException('Delete single data fail,model not exists.');
        }

        $model = $query->first();

        try {
            $model->delete();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new HttpRuntimeException('Delete model fail.');
        }

        return true;
    }

    public function batch(array $values, string $by, Builder $builder): int
    {
        $models = $builder->whereIn($by, $values)->get();

        return array_reduce($models, function (int $count, Model $model) {

            if ($model->delete()) {
                $count++;
            }

            return $count;
        }, 0);
    }

}