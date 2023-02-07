<?php

namespace Xgbnl\Cloud\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install laravel cloud';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            "--provider" => "Xgbnl\Cloud\CloudServiceProvider"
        ]);
    }
}
