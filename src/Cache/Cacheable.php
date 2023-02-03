<?php

namespace Xgbnl\Cloud\Cache;

use HttpException;
use Redis;
use RedisException;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Illuminate\Support\Facades\Redis as FacadesRedis;

/**
 * @method static void destroyCache(string $key = null) 销毁缓存
 * @method static void storeCache(mixed ...$params) 存储缓存
 * @method static mixed resourcesCache(string $key = null) 获取缓存
 */
abstract class Cacheable
{
    use CallMethodCollection;

    protected readonly ?Repositories $repositories;

    protected readonly ?Redis $redis;

    protected ?string $primary = null;

    final public function __construct(Repositories $repositories = null)
    {
        $this->repositories = $repositories ?? $this->resolve();

        try {
            $this->redis = FacadesRedis::connection(env('CACHEABLE', 'default'))->client();
        } catch (RedisException $e) {
            $this->trigger(500, '缓存服务初始化失败:[ ' . $e->getMessage() . ' ]');
        }
    }

    final public function destroy(string $key = null): void
    {
        try {
            if ($this->redis->exists($this->primary ?? $key)) {
                $this->redis->del($this->primary ?? $key);
            }
        } catch (RedisException $e) {
            $this->trigger(500, '删除缓存失败:[ ' . $e->getMessage() . ' ]');
        }
    }

    private function resolve(): ?Repositories
    {
        $clazz = $this->customSubStr(get_class($this), '\\', true);

        $clazz = strEndWith($clazz, 'Cache');

        if (is_null($this->primary)) {
            $this->primary = 'cache:' . strtolower($clazz);
        }

        $class = 'App\\Repositories\\' . $clazz . 'Repository';

        return !class_exists($class) ? null : app($class);
    }

    /**
     * @throws HttpException
     */
    public static function __callStatic(string $method, mixed $parameters): mixed
    {
        $method = strEndWith($method, 'Cache');

        if (!method_exists(static::class, $method)) {
            throw new HttpException('缓存模型调用静态代理时失败，不存在方法：[' . $method . ' ]');
        }

        return empty($parameters) ? (new static())->{$method}() : (new static())->{$method}(...$parameters);
    }

    abstract public function resources(string $key = null): mixed;

    abstract public function store(mixed ...$params): void;
}