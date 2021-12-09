<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\Export;

interface ImportableCrud
{
    public function getImport(): Export;

    public function getImportParameters(): array;
}
