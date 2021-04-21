<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ExportableCrud;
use Thomascombe\BackpackAsyncExport\Jobs\ExportJob;

trait HasExportButton
{
    /**
     * @throws \Exception
     */
    protected function addExportButtons()
    {
        $this->checkInterfaceImplementation();
        $this->crud->addButton('top', 'export', 'view', 'backpack-async-export::buttons/export', 'end');
    }

    /**
     * @throws \Exception
     */
    protected function setupExportRoutes($segment, $routeName, $controller)
    {
        $this->checkInterfaceImplementation();

        Route::get($segment . '/' . config('backpack-async-export.admin_route'), [
            'as' => $routeName.'.export',
            'uses' => $controller.'@export',
            'operation' => 'export',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function export(): RedirectResponse
    {
        $this->checkInterfaceImplementation();

        list($export, $parameters) = $this->getExport();

        ExportJob::dispatch($export, $parameters);
        \Alert::info(__('backpack-async-export::export.notifications.queued'))->flash();

        return response()->redirectToRoute('export.index');
    }

    /**
     * @throws \Exception
     */
    protected function checkInterfaceImplementation(): void
    {
        if (!$this instanceof ExportableCrud) {
            throw new \Exception(sprintf('%s need to implement %s', self::class, ExportableCrud::class));
        }
    }
}
