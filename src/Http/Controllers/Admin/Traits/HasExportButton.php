<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Thomascombe\BackpackAsyncExport\Jobs\ExportJob;

trait HasExportButton
{
    protected function addExportButtons()
    {
        $this->crud->addButton('top', 'export', 'view', 'backpack_async_export::buttons/export', 'end');
    }

    protected function setupExportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/' . config('backpack_async_export.admin_route'), [
            'as' => $routeName.'.export',
            'uses' => $controller.'@export',
            'operation' => 'export',
        ]);
    }

    public function export(): RedirectResponse
    {
        list($export, $parameters) = $this->getExport();

        ExportJob::dispatch($export, $parameters);
        \Alert::info(__('backpack_async_export::export.notifications.queued'))->flash();

        return response()->redirectToRoute('export.index');
    }
}
