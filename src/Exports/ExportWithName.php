<?php

namespace Thomascombe\BackpackAsyncExport\Exports;

interface ExportWithName
{
    public static function getName(): string;
}
