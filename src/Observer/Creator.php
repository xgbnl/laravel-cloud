<?php

namespace Xgbnl\Cloud\Observer;

use Xgbnl\Cloud\Enum\Trigger;
use Xgbnl\Cloud\Utils\Fail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Log};

class Creator extends Observable
{
    public function create(array $attributes): Model
    {
        $this->model = $this->service->query->create($attributes);

        $this->triggerEvent(Trigger::Created);

        $this->notifyObserver();

        return $this->model;
    }

    public function transactionCreate(array $attributes): Model
    {
        try {

            DB::beginTransaction();
            $this->model = $this->service->query->create($attributes);
            DB::commit();

            $this->triggerEvent(Trigger::Created);

        } catch (Throwable $e) {
            DB::rollBack();

            $msg = '创建数据错误 [ ' . $e->getMessage() . ' ]';
            Log::error($msg);
            throw  new \RuntimeException($msg, 500, $e);
        }

        $this->notifyObserver();

        return $this->model;
    }
}