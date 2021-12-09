<?php

namespace Thomascombe\BackpackAsyncExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\Export;

class ImportJob implements ShouldQueue
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
        if ($this->export->action_type !== ActionType::Import) {
            $message = sprintf('Import of type "%s" try to be import', $this->export->action_type);
            $this->export->update([
                Export::COLUMN_STATUS => ExportStatus::Error,
                Export::COLUMN_ERROR => $message,
            ]);
            Log::error($message);
            return;
        }

        $this->export->update([
            Export::COLUMN_STATUS => ExportStatus::Processing,
        ]);

        try {
            ini_set('memory_limit', config('backpack-async-export.export_memory_limit'));
            $exportClass = $this->export->{Export::COLUMN_EXPORT_TYPE};

            Excel::import(
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
            Log::error(__('backpack-async-export::import.errors.global-export'), ['exception' => $exception]);
        }
    }
}
