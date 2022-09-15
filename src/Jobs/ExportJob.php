<?php

namespace Thomascombe\BackpackAsyncExport\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Exports\SimpleCsv as SimpleCsvExport;
use Thomascombe\BackpackAsyncExport\Jobs\Export\AfterSuccess;
use Thomascombe\BackpackAsyncExport\Jobs\Export\SimpleCsv;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;
use Throwable;

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
            $instance = new $exportClass(...$this->exportParameters);

            $chain = [
                new AfterSuccess($this->export),
            ];

            if ($instance instanceof SimpleCsvExport) {
                $queue = config('backpack-async-import-export.simple_csv_export.queue');

                SimpleCsv
                    ::dispatch($instance, $this->export)
                    ->chain($chain)
                    ->onQueue($queue)
                    ->allOnQueue($queue);

                return $this;
            }

            $result = Excel::store(
                $instance,
                $this->export->{ImportExport::COLUMN_FILENAME},
                config('backpack-async-import-export.disk')
            );

            if ($result instanceof PendingDispatch) {
                $result->chain($chain);
            } else {
                $this->export->update([
                    ImportExport::COLUMN_STATUS => ExportStatus::Successful,
                    ImportExport::COLUMN_COMPLETED_AT => now(),
                ]);
            }
        } catch (Throwable $exception) {
            $this->export->update([
                ImportExport::COLUMN_STATUS => ExportStatus::Error,
                ImportExport::COLUMN_ERROR => $exception->getMessage(),
            ]);
            Log::error(__('backpack-async-export::export.errors.global-export'), ['exception' => $exception]);
        }
    }
}
