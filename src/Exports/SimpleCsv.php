<?php

namespace Thomascombe\BackpackAsyncExport\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

abstract class SimpleCsv implements FromQuery, WithHeadings, WithMapping
{
    public function getChunkSize(): int
    {
        return config('excel.exports.chunk_size', 1000);
    }
}
