<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\ImportExport;

interface ExportableCrud
{
    public function getExport(): ImportExport;

    /**
     * @return array<string, mixed>
     */
    public function getExportParameters(): array;
}
