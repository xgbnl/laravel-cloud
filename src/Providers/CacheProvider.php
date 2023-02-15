<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Repositories\Repository;

final  class CacheProvider extends Provider implements Factory
{
    public function make(string $abstract): Repository
    {
        return match ($abstract) {
            'repository' => $this->resolve($abstract),
        };
    }

    protected function resolve(string $abstract, array $parameters = []): Repository
    {
        $class = $this->resolveClass($abstract);

        if (!is_subclass_of($class, Repository::class)) {
            throw new FailedResolveException('仓库层文件[' . $class . ']必须继承[' . Repository::class);
        }

        return $this->build($class);
    }

    public function resolveClass(string $abstract = null): string
    {
        if (!$this->dominator->isNull()) {
            $this->build($this->dominator->getModelName());
        }

        ['namespace' => $ns, 'class' => $class] = $this->explode();

        $class = $this->splice($class, 'Cache');

        $class = $ns . '\\Repositories\\' . $class . ucwords($abstract);

        if (!class_exists($class)) {
            throw new FailedResolveException('当前缓存层模型[' . get_called_class() . ']调用的仓库层模型[' . $class . ']不存在');
        }

        return $this->dominator->assign($class);
    }
}