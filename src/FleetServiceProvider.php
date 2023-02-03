<?php

namespace Xgbnl\Cloud;

use Illuminate\Support\ServiceProvider;
use Xgbnl\Cloud\Commands\InstallCommand;
use Xgbnl\Cloud\Commands\MakeCacheCommand;
use Xgbnl\Cloud\Commands\MakeObserverCommand;
use Xgbnl\Cloud\Commands\MakeRepositoryCommand;
use Xgbnl\Cloud\Commands\MakeServiceCommand;
use Xgbnl\Cloud\Commands\MakeTransformCommand;

class FleetServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        MakeCacheCommand::class,
        MakeRepositoryCommand::class,
        MakeTransformCommand::class,
        MakeObserverCommand::class,
        MakeServiceCommand::class,
    ];

    public function boot(): void
    {
        $this->installCommand($this->commands);
    }

    protected function installCommand(array $commands): void
    {
        $this->publishes([__DIR__ . '/Commands/Stubs/BaseController.stub' => app_path('Http/Controllers/BaseController.php')]);
        $this->commands($commands);
    }
}
