<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

interface ExportableCrud
{
    public function getExport(): array;
}
