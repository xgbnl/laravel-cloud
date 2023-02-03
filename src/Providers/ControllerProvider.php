<?php

namespace Xgbnl\Cloud\Providers;

use ReflectionException;
use Xgbnl\Cloud\Cache\Cacheable;
use Xgbnl\Cloud\Contacts\Factory;
use Xgbnl\Cloud\Exceptions\FailedResolveException;
use Xgbnl\Cloud\Repositories\Repositories;
use Xgbnl\Cloud\Repositories\Repository;
use Xgbnl\Cloud\Services\Service;

final readonly class ControllerProvider extends Provider implements Factory
{
    public function make(string $abstract): Service|Repository|Cacheable
    {
        return match ($abstract) {
            'repository', 'service' => $this->resolve($abstract),
            'cache'                 => $this->resolve($abstract, ['repository' => $this->dominator->repository]),
        };
    }

    protected function resolve(string $abstract, array $parameters = []): Service|Repository|Cacheable
    {
        $class = $this->resolveClass($abstract);

        [$parent, $name] = match ($abstract) {
            'repository' => [Repositories::class, '仓库'],
            'Service'    => [Service::class, '服务'],
            'cache'      => [Cacheable::class, '缓存'],
        };

        if (!is_subclass_of($class, $parent)) {
            throw new FailedResolveException('控制器分层' . $name . '模型[ ' . $class . ' ]必须继承[ ' . $parent . ' ]');
        }

        return $this->build($class, $parameters);
    }

    final public function resolveClass(string $abstract = null): string
    {
        if (!$this->dominator->isNull() && str_ends_with($this->dominator->getModelName(), $abstract)) {
            return $this->dominator->getModelName();
        }

        $clazz = str_replace('\\http\\Controllers\\', '\\', $this->dominator->getCalledClass());

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

    protected function failedResolved(string $abstract = null, string $controller = null,bool $exists = false): void
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