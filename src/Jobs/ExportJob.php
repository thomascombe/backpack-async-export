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
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

class ExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ImportExport $export;
    private array $exportParameters;

    public function __construct(ImportExport $export, ...$exportParameters)
    {
        $this->export = $export;
        $this->exportParameters = $exportParameters;
    }

    public function handle()
    {
        $this->export->update([
            ImportExport::COLUMN_STATUS => ExportStatus::Processing,
        ]);

        try {
            ini_set('memory_limit', config('backpack-async-import-export.export_memory_limit'));
            $exportClass = $this->export->{ImportExport::COLUMN_EXPORT_TYPE};

            Excel::store(
                new $exportClass(...$this->exportParameters),
                $this->export->{ImportExport::COLUMN_FILENAME},
                config('backpack-async-import-export.disk')
            );

            $this->export->{ImportExport::COLUMN_STATUS} = ExportStatus::Successful;
            $this->export->save();
            $this->export->update([
                ImportExport::COLUMN_STATUS => ExportStatus::Successful,
                ImportExport::COLUMN_COMPLETED_AT => now(),
            ]);
        } catch (\Exception | \Throwable $exception) {
            $this->export->update([
                ImportExport::COLUMN_STATUS => ExportStatus::Error,
                ImportExport::COLUMN_ERROR => $exception->getMessage(),
            ]);
            Log::error(__('backpack-async-export::export.errors.global-export'), ['exception' => $exception]);
        }
    }
}
