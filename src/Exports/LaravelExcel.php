<?php

namespace Thomascombe\BackpackAsyncExport\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;
use Throwable;

abstract class LaravelExcel
{
    use Exportable;

    /**
     * @var ImportExport
     */
    protected $model;

    public function failed(Throwable $exception): void
    {
        if (null !== ($model = $this->getModel())) {
            $model->update([
                ImportExport::COLUMN_STATUS => ExportStatus::Error,
                ImportExport::COLUMN_ERROR => $exception->getMessage(),
            ]);
        }

        Log::error(
            $exception->getMessage(),
            ['exception' => $exception]
        );
    }

    public function getModel(): ?ImportExport
    {
        return $this->model;
    }

    public function setModel(ImportExport $model): self
    {
        $this->model = $model;

        return $this;
    }
}
