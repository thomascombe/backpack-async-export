<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\Export;

interface ExportableCrud
{
    public function getExport(): Export;

    public function getExportParameters(): array;
}
