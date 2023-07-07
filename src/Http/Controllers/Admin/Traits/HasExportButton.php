<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ExportableCrud;
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\MultiExportableCrud;
use Thomascombe\BackpackAsyncExport\Jobs\ExportJob;

/**
 * Trait HasExportButton
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits
 *
 * @mixin CrudController
 * @mixin ExportableCrud
 */
trait HasExportButton
{
    /**
     * @throws Exception
     */
    protected function setupExportDefaults(): void
    {
        $this->crud->allowAccess('export');

        $this->crud->operation('list', function () {
            $this->addExportButtons();
        });
    }

    /**
     * @throws Exception
     */
    protected function addExportButtons(): void
    {
        $this->checkExportInterfaceImplementation();

        $exports = [MultiExportableCrud::DEFAULT_EXPORT_NAME => null];
        if ($this instanceof MultiExportableCrud) {
            $exports = $this->getAvailableExports();
            $this->checkExportMethod($exports);
        }

        $this->crud->setting('exports', $exports);
        $this->crud->addButton('top', 'export', 'view', 'backpack-async-export::buttons/export', 'end');
    }

    /**
     * @throws Exception
     */
    protected function setupExportRoutes(string $segment, string $routeName, string $controller): void
    {
        $this->checkExportInterfaceImplementation();

        Route::get($segment . '/' . config('backpack-async-import-export.admin_export_route'), [
            'as' => $routeName . '.export',
            'uses' => $controller . '@export',
            'operation' => 'export',
        ]);
    }

    /**
     * @throws Exception
     */
    public function export(): RedirectResponse
    {
        $this->checkExportInterfaceImplementation();

        $export = request()->query(MultiExportableCrud::QUERY_PARAM, MultiExportableCrud::DEFAULT_EXPORT_NAME);
        abort_if(
            MultiExportableCrud::DEFAULT_EXPORT_NAME !== $export &&
            (!$this instanceof MultiExportableCrud || !in_array($export, array_keys($this->getAvailableExports()))),
            Response::HTTP_UNAUTHORIZED
        );
        $exportModel = $this->{$this->getExportMethodName($export)}();
        $parameters = $this->{$this->getExportParametersMethodName($export)}();

        ExportJob::dispatch($exportModel, ...$parameters);
        Alert::info(__('backpack-async-export::export.notifications.queued'))->flash();

        return response()->redirectToRoute(config('backpack-async-import-export.admin_export_route') . '.index');
    }

    /**
     * @throws Exception
     */
    protected function checkExportInterfaceImplementation(): void
    {
        if (!$this instanceof ExportableCrud) {
            throw new Exception(sprintf('%s need to implement %s', self::class, ExportableCrud::class));
        }
    }

    /**
     * @throws Exception
     */
    protected function checkExportMethod(array $exports): void
    {
        foreach ($exports as $exportKey => $exportName) {
            $exportMethodName = $this->getExportMethodName($exportKey);
            $exportParametersMethodName = $this->getExportParametersMethodName($exportKey);
            foreach ([$exportMethodName, $exportParametersMethodName] as $methodName) {
                if (!method_exists($this, $methodName)) {
                    throw new Exception(
                        sprintf('%s need method "%s"', self::class, $methodName)
                    );
                }
            }
        }
    }

    protected function getExportMethodName(string $export): string
    {
        $export = MultiExportableCrud::DEFAULT_EXPORT_NAME !== $export ? $export : '';
        return sprintf('getExport%s', Str::studly($export));
    }

    protected function getExportParametersMethodName(string $export): string
    {
        $export = MultiExportableCrud::DEFAULT_EXPORT_NAME !== $export ? $export : '';
        return sprintf('getExport%sParameters', Str::studly($export));
    }
}
