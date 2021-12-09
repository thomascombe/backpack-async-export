<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\ImportExport;

interface ExportableCrud
{
    public function getExport(): ImportExport;

    public function getExportParameters(): array;
}
