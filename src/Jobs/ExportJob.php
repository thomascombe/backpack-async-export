<?php

namespace Thomascombe\BackpackAsyncExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\Export;

class ExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            ini_set('memory_limit', config('backpack-async-export.export_memory_limit'));
            $exportClass = $this->export->{Export::COLUMN_EXPORT_TYPE};

            Excel::store(
                new $exportClass(...$this->exportParameters),
                $this->export->{Export::COLUMN_FILENAME},
                config('backpack-async-export.disk')
            );

            $this->export->{Export::COLUMN_STATUS} = ExportStatus::Successful;
            $this->export->save();
            $this->export->update([
                Export::COLUMN_STATUS => ExportStatus::Successful,
                Export::COLUMN_COMPLETED_AT => now(),
            ]);
        } catch (\Exception | \Throwable $exception) {
            $this->export->update([
                Export::COLUMN_STATUS => ExportStatus::Error,
                Export::COLUMN_ERROR => $exception->getMessage(),
            ]);
            Log::error(__('backpack-async-export::export.errors.global-export'), ['exception' => $exception]);
        }
    }
}
