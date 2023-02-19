<?php

namespace Xgbnl\Cloud\Cache;

use HttpException;
use Redis;
use RedisException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @method static void destroyCache(string $key = null) 销毁缓存
 * @method static void storeCache(mixed ...$params) 存储缓存
 * @method static mixed resourcesCache(string $key = null) 获取缓存
 * @property Repository $repository
 */
abstract class Cacheable implements Contextual
{
    use  ContextualTrait;

    protected readonly ?Redis $redis;

    protected readonly Factory $factory;

    protected ?string $primary = null;

    final public function __construct(Factory $factory, Redis $redis)
    {
        $this->factory = $factory;

        $this->redis = $redis;

        if (!$this->primary) {
            $class = $this->getAlias();

            $class = str_ends_with($class, 'Cache')
                ? substr($class, 0, strpos($class, 'Cache'))
                : $class;

            $this->primary = 'cache:' . strtolower($class);
        }
    }

    final public function destroy(string $key = null): void
    {
        try {
            if ($this->redis->exists($this->primary ?? $key)) {
                $this->redis->del($this->primary ?? $key);
            }
        } catch (RedisException $e) {
            $this->abort(500, '删除缓存失败:[ ' . $e->getMessage() . ' ]');
        }
    }

    /**
     * @throws HttpException
     */
    public static function __callStatic(string $method, mixed $parameters): mixed
    {
        if (str_ends_with($method, 'Cache')) {
            $method = substr($method, 0, -strlen('Cache'));
        }

        if (!method_exists(static::class, $method)) {
            throw new HttpException('缓存模型调用静态代理时失败，不存在方法：[' . $method . ' ]');
        }

        return empty($parameters) ? (new static())->{$method}() : (new static())->{$method}(...$parameters);
    }

    abstract public function resources(string $key = null): mixed;

    abstract public function store(mixed ...$params): void;
}