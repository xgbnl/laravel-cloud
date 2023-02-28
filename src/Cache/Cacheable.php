<?php

namespace Xgbnl\Cloud\Cache;

use HttpException;
use HttpRuntimeException;
use Redis;
use RedisException;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @method static void destroyCache(string $key = null)
 * @method static void storeCache(mixed ...$params)
 * @method static mixed resourcesCache(string $key = null)
 * @property Repository $repository
 */
abstract readonly class Cacheable implements Contextual
{
    use  ContextualTrait;

    protected ?Redis $redis;

    protected Factory $factory;

    final public function __construct(Factory $factory, Redis $redis)
    {
        $this->factory = $factory;
        $this->redis = $redis;
    }

    /**
     * @throws HttpRuntimeException
     */
    final public function destroy(string $key = null): void
    {
        $identifier = $key ?? $this->getIdentifier();

        try {
            if ($this->redis->exists($identifier)) {
                $this->redis->del($identifier);
            }
        } catch (RedisException $e) {
            throw new HttpRuntimeException('destroy cache fail or cache:[' . $this->getIdentifier() . ' not exists.]', 500);
        }
    }

    final public function getIdentifier(): string
    {

    }

    /**
     * @throws HttpException
     * @throws ReflectionException
     */
    public static function __callStatic(string $method, mixed $parameters): mixed
    {
        if (str_ends_with($method, 'Cache')) {
            $method = substr($method, 0, -strlen('Cache'));
        }
        return Application::getInstance()->make(static::class)->{$method}(...$parameters);
    }

    abstract public function resources(string $key = null): mixed;

    abstract public function store(mixed ...$params): void;
}