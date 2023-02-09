<?php

namespace Xgbnl\Cloud\Exporter;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Vtiful\Kernel\Excel;
use Xgbnl\Cloud\Services\Service;
use Xgbnl\Cloud\Traits\CallMethodCollection;
use Xgbnl\Cloud\Contacts\Exporter as ExporterContact;

/**
 * @method static void execute()
 */
abstract class Exporter
{
    use CallMethodCollection;

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

        return $excel->fileName(Str::random(32) . '.xlsx', $this->outputDir)->header($this->headers());
    }

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
            $message = '[导出' . $outputName . '] 导出失败，文件不存在或不可读';

            Log::error($message);
            $this->abort(500, $message);
        }
    }

    public static function __callStatic(string $name, array $arguments): void
    {
        if ($name === 'execute') {
            self::exporter()->export();
        }
    }

    private static function exporter(): ExporterContact
    {
        return new static();
    }

    abstract protected function headers(): array;
}