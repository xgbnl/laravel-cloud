<?php

namespace Xgbnl\Cloud\Cache;

use HttpRuntimeException;
use Redis;
use RedisException;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Proxy\Factory;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Support\Str;
use Xgbnl\Cloud\Traits\ContextualTrait;

/**
 * @method static void destroy(string $key = null)
 * @method static void store(mixed ...$params)
 * @method static mixed resources(string $key = null)
 * @property Repository $repository
 */
abstract readonly class Cacheable implements Contextual
{
    use  ContextualTrait;

    protected ?Redis $redis;

    private Factory $factory;

    private Str $str;

    final public function __construct(Factory $factory, Redis $redis, Str $str)
    {
        $this->factory = $factory;
        $this->redis = $redis;
        $this->str = $str;
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
        return 'cacheable:' . $this->str->split(static::class, 'Cache');
    }

    /**
     * @throws ReflectionException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return Application::getInstance()->make(static::class)->{$name}(...$arguments);
    }

    /**
     * Get cache resources.
     * @param string|null $key
     * @return mixed
     */
    abstract public function resources(string $key = null): mixed;

    /**
     * Store cache resources.
     * @param mixed ...$params
     * @return void
     */
    abstract public function store(mixed ...$params): void;
}