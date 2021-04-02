<?php

namespace Thomascombe\BackpackAsyncExport;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thomascombe\BackpackAsyncExport\BackpackAsyncExport
 */
class BackpackAsyncExportFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'backpack_async_export';
    }
}
