<?php

namespace Thomascombe\BackpackAsyncExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Enums\ImportExportStatus;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

class ImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ImportExport $export;
    readonly private array $exportParameters;

    public function __construct(ImportExport $export, array ...$exportParameters)
    {
        $this->export = $export;
        $this->exportParameters = $exportParameters;
    }

    public function handle(): void
    {
        if ($this->export->action_type !== ActionType::Import) {
            $message = sprintf('Import of type "%s" try to be import', $this->export->action_type->value);
            $this->export->update([
                ImportExport::COLUMN_STATUS => ImportExportStatus::Error,
                ImportExport::COLUMN_ERROR => $message,
            ]);
            Log::error($message);
            return;
        }

        $this->export->update([
            ImportExport::COLUMN_STATUS => ImportExportStatus::Processing,
        ]);

        try {
            ini_set('memory_limit', config('backpack-async-import-export.export_memory_limit'));
            $exportClass = $this->export->{ImportExport::COLUMN_EXPORT_TYPE};

            unset($this->exportParameters['private']);
            $importObject = new $exportClass(...$this->exportParameters);
            Excel::import(
                $importObject,
                $this->export->{ImportExport::COLUMN_FILENAME},
                config('backpack-async-import-export.disk')
            );

            $this->export->{ImportExport::COLUMN_STATUS} = ImportExportStatus::Successful;
            $this->export->save();
            $this->export->update([
                ImportExport::COLUMN_STATUS => ImportExportStatus::Successful,
                ImportExport::COLUMN_COMPLETED_AT => now(),
            ]);

            if (method_exists($importObject, 'failures') && $importObject->failures()->isNotEmpty()) {
                $message = $importObject
                    ->failures()
                    ->map(fn($item, $key) => $item->errors())
                    ->flatten()
                    ->implode(', ');
                throw new \Exception($message);
            }
        } catch (\Exception|\Throwable $exception) {
            $this->export->update([
                ImportExport::COLUMN_STATUS => ImportExportStatus::Error,
                ImportExport::COLUMN_ERROR => $exception->getMessage(),
            ]);
            Log::error(__('backpack-async-export::import.errors.global-export'), ['exception' => $exception]);
        } finally {
            Storage::disk($this->export->{ImportExport::COLUMN_DISK})->delete(
                $this->export->{ImportExport::COLUMN_FILENAME}
            );
        }
    }
}
