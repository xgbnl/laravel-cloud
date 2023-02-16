<?php

namespace Xgbnl\Cloud\Providers;

use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Services\Service;

final class ControllerProvider extends Provider implements Factory
{
    public function resolve(string $abstract): Service|Repository|Cacheable
    {
        return match ($abstract) {
            'repository', 'service', 'cache' => $this->make($abstract),
            default                          => throw new FailedResolveException('错误的属性调用，或属性[' . $abstract . ']不存在'),
        };
    }

    protected function make(string $abstract): Service|Repository|Cacheable
    {
        $class = $this->resolveClass($abstract);

        [$parent, $name] = match ($abstract) {
            'repository' => [Repositories::class, '仓库'],
            'service'    => [Service::class, '服务'],
            'cache'      => [Cacheable::class, '缓存'],
        };

        if (!is_subclass_of($class, $parent)) {
            throw new FailedResolveException('控制器调用[' . $name . ']分层模型[ ' . $class . ' ]必须继承[ ' . $parent . ' ]');
        }

        return $this->build($class);
    }

    final public function resolveClass(string $abstract = null): string
    {
        if (!$this->dominator->has() && str_ends_with($this->dominator->getModelName(), $abstract)) {
            return $this->dominator->getModelName();
        }

        $clazz = str_replace('\\http\\Controllers\\', '\\', $this->dominator->getAlias());

        $parts = explode('\\', $clazz);

        $ns = match ($abstract) {
            'service'    => array_shift($parts) . '\\Services\\',
            'repository' => array_shift($parts) . '\\Repositories\\',
            'request'    => array_shift($parts) . '\\Http\\Requests\\',
            'cache'      => array_shift($parts) . '\\Cache\\',
        };

        $controller = array_pop($parts);

        $class = $this->splice($controller, 'Controller');

        $class = $ns . $class . ucwords($abstract);

        if (!class_exists($class)) {
            $this->failedResolved($class, $controller);
        }

        return $this->dominator->assign($class);
    }

    protected function failedResolved(string $abstract = null, string $controller = null): void
    {
        $modelType = match (true) {
            str_ends_with($abstract, 'Request')    => '验证器',
            str_ends_with($abstract, 'Service')    => '服务层',
            str_ends_with($abstract, 'Repository') => '仓库层',
            str_ends_with($abstract, 'Cache')      => '缓存层',
        };

        throw new FailedResolveException('控制器[ ' . $controller . ' ]' . '调用' . $modelType . '失败,文件[ ' . $abstract . ' ]不存在');
    }
}