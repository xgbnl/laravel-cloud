<?php

namespace Xgbnl\Cloud\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeExporterCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:exporter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new exporter.';

    protected $type = 'Exporter';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/'.$this->type.'.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Exporters';
    }
}