<?php

namespace Xgbnl\Cloud\Exporter;

use Illuminate\Support\Facades\Storage;
use ReflectionException;
use Vtiful\Kernel\Excel;
use Xgbnl\Cloud\Kernel\Application;
use Xgbnl\Cloud\Services\Service;

/**
 * @method static void export()
 */
abstract class Exporter
{
    protected string $outputDir = 'exports';

    protected readonly Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    final protected function getExcel(): Excel
    {
        if (!Storage::exists($this->outputDir)) {
            Storage::makeDirectory($this->outputDir);
        }

        $excel = new Excel(['path' => Storage::path($this->outputDir)]);

        return $excel->fileName(date('YmdHis') . rand(100000, 999999) . '.xlsx', $this->outputDir)->header($this->headers());
    }

    /**
     * @throws \HttpRuntimeException
     */
    final protected function setHeader(mixed $filePath, string $outputName): void
    {
        if (is_file($filePath) && is_readable($filePath)) {
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Disposition: attachment;filename="' . $outputName . ' - ' . date('YmdHis') . '.xlsx"');
            header('Content-Length: ' . filesize($filePath));
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Cache-Control: max-age=0');
            header('Pragma: public');

            if (ob_get_length() > 0) {
                ob_clean();
            }
            flush();

            copy($filePath, 'php://output');

            @unlink($filePath);
        } else {
            throw new \HttpRuntimeException('export [' . $outputName . '] fail,file read fail or not exists.', 500);
        }
    }

    /**
     * @throws ReflectionException
     */
    public static function __callStatic(string $name, array $arguments): void
    {
        Application::getInstance()->make(static::class)->{$name}();
    }

    abstract protected function headers(): array;
}