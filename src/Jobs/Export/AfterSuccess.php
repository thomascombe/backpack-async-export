<?php

namespace Thomascombe\BackpackAsyncExport\Jobs\Export;

use Carbon\Carbon as Date;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

class AfterSuccess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ImportExport $model;

    public function __construct(ImportExport $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->model->update([
            ImportExport::COLUMN_STATUS => ExportStatus::Successful,
            ImportExport::COLUMN_COMPLETED_AT => Date::now(),
        ]);
    }
}
