<?php

namespace Xgbnl\Cloud\Services\Process\Deleter;

use Xgbnl\Cloud\Exceptions\ServiceException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final readonly class Deleter
{
    public function single(int|string $value, string $by, Builder $builder): bool
    {
        $query = $builder->where($by, $value);

        if (!(clone $query)->exists()) {
            throw new ServiceException('The model does not exist or has been deleted.', 500);
        }

        $model = $query->first();

        try {
            $model->delete();
        } catch (\Exception $e) {
            throw new ServiceException('Delete model fail.[' . $e->getMessage() . ']', 500);
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