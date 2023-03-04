<?php

namespace Xgbnl\Cloud;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Cloud\Commands\InstallCommand;
use Xgbnl\Cloud\Commands\MakeCacheCommand;
use Xgbnl\Cloud\Commands\MakeExporterCommand;
use Xgbnl\Cloud\Commands\MakeRepositoryCommand;
use Xgbnl\Cloud\Commands\MakeServiceCommand;
use Xgbnl\Cloud\Commands\MakeTransformCommand;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Support\Constant;

class CloudServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        MakeCacheCommand::class,
        MakeRepositoryCommand::class,
        MakeTransformCommand::class,
        MakeServiceCommand::class,
        MakeExporterCommand::class,
    ];

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Commands/Stubs/BaseController.stub' => app_path('Http/Controllers/BaseController.php'),
            __DIR__ . '/Commands/Config/laravel-cloud.php'  => base_path('config/laravel-cloud.php')
        ]);
        $this->commands($this->commands);

        $this->register();
    }

    public function register(): void
    {
        $app = Application::getInstance();

        $app->singleton('constant', fn() => new Constant());

        $app->singleton(\Redis::class, fn() => Redis::connection(config('laravel-cloud.cacheable'))->client());
    }
}
