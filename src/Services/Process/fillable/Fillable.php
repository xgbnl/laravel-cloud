<?php

namespace Xgbnl\Cloud\Services\Process\fillable;

use HttpRuntimeException;
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

            return $this->update([$by => $byValue], $data, $builder);
        }

        return $this->create($data, $builder);
    }

    abstract protected function update(array $attributes, array $data, Builder $builder): Model|Builder;

    abstract protected function create(array $data, Builder $builder): Model|Builder;

    /**
     * @throws HttpRuntimeException
     */
    final protected function fillFailException(): void
    {
        throw new HttpRuntimeException('Create or update model fail.');
    }
}