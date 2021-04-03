<?php

namespace Thomascombe\BackpackAsyncExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\Export;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Export $export;
    private array $exportParameters;

    public function __construct(Export $export, ...$exportParameters)
    {
        $this->export = $export;
        $this->exportParameters = $exportParameters;
    }

    public function handle()
    {
        $this->export->update([
            Export::COLUMN_STATUS => ExportStatus::Processing,
        ]);

        try {
            ini_set('memory_limit', config('backpack_async_export.export_memory_limit'));
            $exportClass = $this->export->{Export::COLUMN_EXPORT_TYPE};

            Excel::store(new $exportClass(...$this->exportParameters), $this->export->{Export::COLUMN_FILENAME});

            $this->export->{Export::COLUMN_STATUS} = ExportStatus::Successful;
            $this->export->save();
            $this->export->update([
                Export::COLUMN_STATUS => ExportStatus::Successful,
                Export::COLUMN_COMPLETED_AT => now(),
            ]);
        } catch (\Exception|\Throwable $exception) {
            $this->export->update([
                Export::COLUMN_STATUS => ExportStatus::Error,
                Export::COLUMN_ERROR => $exception->getMessage(),
            ]);
        }
    }
}
