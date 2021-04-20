<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces;

interface ExportableCrud
{
    function getExport(): array;
}
