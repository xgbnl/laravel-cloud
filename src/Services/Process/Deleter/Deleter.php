<?php

namespace Xgbnl\Cloud\Services\Process\Deleter;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final readonly class Deleter
{
    /**
     * @throws Exception
     */
    public function single(int|string $value, string $by, Builder $builder): bool
    {
        $query = $builder->where($by, $value);

        if (!(clone $query)->exists()) {
            throw new Exception('Delete single data fail,model not exists.', 500);
        }

        $model = $query->first();

        try {
            $model->delete();
        } catch (\Exception $e) {
            throw new Exception('Delete model fail.[' . $e->getMessage() . ']', 500);
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