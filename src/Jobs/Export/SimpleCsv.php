<?php

namespace Thomascombe\BackpackAsyncExport\Jobs\Export;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Exports\SimpleCsv as Export;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;
use Throwable;

class SimpleCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Export $export;
    protected ImportExport $model;

    /**
     * @var bool|resource|closed-resource
     */
    protected $handle;

    public function __construct(Export $export, ImportExport $model)
    {
        $this->export = $export;
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $this->openFile();
            $this->addUtf8Header();

            fputcsv($this->handle, $this->export->headings());

            $this->export->query()->chunk(
                $this->export->getChunkSize(),
                function (Collection $collection): void {
                    $collection->each(
                        function (Eloquent $model): void {
                            fputcsv(
                                $this->handle,
                                $this->export->map($model)
                            );
                        }
                    );
                }
            );

            fclose($this->handle);
        } catch (Throwable $exception) {
            $this->model->update([
                ImportExport::COLUMN_STATUS => ExportStatus::Error,
                ImportExport::COLUMN_ERROR => $exception->getMessage(),
            ]);

            Log::error(
                $exception->getMessage(),
                ['exception' => $exception]
            );

            throw $exception;
        }
    }

    /**
     * @return $this
     * @throws Throwable
     */
    protected function openFile(): self
    {
        /**
         * @psalm-suppress UndefinedInterfaceMethod
         */
        $this->handle = fopen(
            Storage::disk($this->model->disk)->path($this->model->filename),
            'w'
        );

        throw_if(
            false === $this->handle,
            new Exception(
                sprintf('Unable to open file %s.', $this->model->filename)
            )
        );

        return $this;
    }

    /**
     * @return void
     */
    public function addUtf8Header(): void
    {
        fprintf($this->handle, sprintf("%s%s%s", chr(0xEF), chr(0xBB), chr(0xBF)));
    }
}
