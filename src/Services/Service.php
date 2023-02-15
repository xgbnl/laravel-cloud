<?php

declare(strict_types=1);

namespace Xgbnl\Cloud\Services;

use Xgbnl\Cloud\Contacts\Exporter;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Dominator;
use Xgbnl\Cloud\Observer\Creator;
use Xgbnl\Cloud\Observer\Deleter;
use Xgbnl\Cloud\Observer\Updater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Xgbnl\Cloud\Providers\ServiceProvider;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Traits\DominatorTrait;

/**
 * @property-read Model $model
 * @property-read string|null $table
 * @property-read EloquentBuilder $query
 * @property-read Exporter $exporter
 */
abstract class Service implements Dominator
{
    use CallMethodCollection, DominatorTrait;

    private ?string $observer = null;

    private readonly Factory $factory;

    final public function __construct(ServiceProvider $provider)
    {
        $this->factory = $provider;

        $this->configure();
    }

    /**
     * 创建或更新数据
     *
     * @param array $data 插入或更新的数据
     * @param string $by 根据此字段更新
     * @param bool $transaction 默认开启事务,用户可关闭自行设置事务
     * @return Model
     */
    final public function createOrUpdate(array $data, string $by = 'id', bool $transaction = true): Model
    {
        if ($byValue = ($data[$by] ?? null)) {
            if ($by === 'id') {
                unset($data[$by]);
            }

            return $transaction
                ? Updater::make($this)->transactionUpdate($data, [$by => $byValue])
                : Updater::make($this)->update($data, [$by => $byValue]);
        }

        return $transaction ? Creator::make($this)->transactionCreate($data) : Creator::make($this)->create($data);
    }

    /**
     * 销毁数据
     * 单独销毁数据可触发观察者
     * @param int|array $value 数组情况下触发批量删除
     * @param string $by 根据给定字段进行删除
     * @return int|bool
     */
    final public function destroy(int|array $value, string $by = 'id'): int|bool
    {
        return is_array($value) ? Deleter::make($this)->batchDelete($value, $by)
            : Deleter::make($this)->delete($value, $by);
    }

    abstract protected function configure(): void;

    /**
     * 为服务注册观察者模型
     * @param string $class
     * @return void
     */
    final protected function registerObserver(string $class): void
    {
        $this->observer = $class;
    }

    /**
     * 获取观察者模型
     * @return string|null
     */
    final public function getObserver(): ?string
    {
        return $this->observer;
    }

    final public function export(): void
    {
        $this->exporter->export();
    }
}
