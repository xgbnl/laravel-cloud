<?php

namespace Xgbnl\Cloud\Cache;

use HttpException;
use Redis;
use RedisException;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Contacts\Properties;
use Xgbnl\Cloud\Providers\CacheProvider;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Illuminate\Support\Facades\Redis as FacadesRedis;
use Xgbnl\Cloud\Traits\PropertiesTrait;

/**
 * @method static void destroyCache(string $key = null) 销毁缓存
 * @method static void storeCache(mixed ...$params) 存储缓存
 * @method static mixed resourcesCache(string $key = null) 获取缓存
 * @method array tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'children') 为列表生成树结构
 * @property Repository $repository
 */
abstract class Cacheable implements Properties
{
    use CallMethodCollection, PropertiesTrait;

    protected readonly ?Redis $redis;

    protected readonly Factory $factory;

    protected ?string $primary = null;

    final public function __construct()
    {
        $this->factory = CacheProvider::bind($this);

        $this->configure();
    }

    private function configure(): void
    {
        try {
            $this->redis = FacadesRedis::connection(env('CACHEABLE', 'default'))->client();
        } catch (RedisException $e) {
            $this->abort(500, '缓存服务初始化失败:[ ' . $e->getMessage() . ' ]');
        }

        if (!$this->primary) {
            $class = get_called_class();

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
        $method = strEndWith($method, 'Cache');

        if (!method_exists(static::class, $method)) {
            throw new HttpException('缓存模型调用静态代理时失败，不存在方法：[' . $method . ' ]');
        }

        return empty($parameters) ? (new static())->{$method}() : (new static())->{$method}(...$parameters);
    }

    abstract public function resources(string $key = null): mixed;

    abstract public function store(mixed ...$params): void;
}