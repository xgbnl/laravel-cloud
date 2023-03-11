<?php

namespace Xgbnl\Cloud\Services\Process\fillable;

use Throwable;
use Xgbnl\Cloud\Exceptions\ServiceException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Cloud\Contacts\Services\FillContact;

abstract class Fillable implements FillContact
{

    protected Model|Builder $model;

    final public function createOrUpdate(mixed $data, string $by, Builder $builder): Builder|Model
    {
        if ($byValue = ($data[$by] ?? null)) {
            if ($by === 'id') {
                unset($data[$by]);
            }

            if (!(clone $builder)->where($by, $byValue)->exists()) {
                throw new ServiceException('The model does not exist and cannot be updated.', 500);
            }

            return $this->update([$by => $byValue], $data, $builder);
        }

        return $this->create($data, $builder);
    }

    abstract protected function update(array $attributes, array $data, Builder $builder): Model|Builder;

    abstract protected function create(array $data, Builder $builder): Model|Builder;

    final protected function fillFailException(Throwable $e): void
    {
        throw new ServiceException('Create or update model fail,' . $e->getMessage(), 500);
    }
}