<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

use Thomascombe\BackpackAsyncExport\Models\ImportExport;

interface ImportableCrud
{
    public function getImport(): ImportExport;

    /**
     * @return array<string, mixed>
     */
    public function getImportParameters(): array;
}
