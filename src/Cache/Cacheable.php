<?php

namespace Xgbnl\Cloud\Cache;

use Redis;
use RedisException;
use ReflectionException;
use Xgbnl\Cloud\Contacts\Controller\Contextual;
use Xgbnl\Cloud\Contacts\Providers\Factory;
use Xgbnl\Cloud\Exceptions\CacheException;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Kernel\Providers\CacheProvider;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Support\Str;
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

    private Factory $factory;

    private Str $str;

    final public function __construct(CacheProvider $factory, Str $str, Redis $redis)
    {
        $this->factory = $factory;
        $this->redis = $redis;
        $this->str = $str;
    }

    final public function destroy(string $key = null): void
    {
        $identifier = $key ?? $this->getIdentifier();

        try {
            if ($this->redis->exists($identifier)) {
                $this->redis->del($identifier);
            }
        } catch (RedisException $e) {
            throw new CacheException('Destroy cache fail.', 500);
        }
    }

    final protected function exists(): bool
    {
        try {
            $exists = $this->redis->exists($this->getIdentifier());
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage(), 500);
        }

        return $exists;
    }

    final public function getIdentifier(): string
    {
        $name = substr(static::class, strrpos(class_exists(static::class), '\\') + 1);
        return 'cacheable:' . $this->getSupport()->split($name, 'Cache');
    }

    /**
     * @throws ReflectionException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $self = Application::getInstance()->make(static::class);

        $method = $self->getSupport()->split($name, 'Cache');

        return $self->{$method}(...$arguments);
    }

    final public function getSupport(): Str
    {
        return $this->str;
    }

    /**
     * Get cache.
     * @param string|null $key
     * @return mixed
     */
    abstract public function resources(string $key = null): mixed;

    /**
     * Store cache.
     * @param mixed|null $values
     * @return void
     */
    abstract public function store(mixed $values = null): void;
}