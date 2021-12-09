<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\ImportExport;

interface ImportableCrud
{
    public function getImport(): ImportExport;

    public function getImportParameters(): array;
}
