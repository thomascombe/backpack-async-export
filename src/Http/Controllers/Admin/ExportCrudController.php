<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Thomascombe\BackpackAsyncExport\Models\Export;

/**
 * Class ExportCrudController
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ExportCrudController extends CrudController
{
    use ListOperation;

    public function setup()
    {
        CRUD::setModel(config('backpack-async-export.export_model'));
        CRUD::setRoute(sprintf('%s/%s', config('backpack.base.route_prefix'), config('backpack-async-export.admin_route')));
        CRUD::setEntityNameStrings(
            __('backpack-async-export::export.name.singular'),
            __('backpack-async-export::export.name.plurial')
        );
        $this->addCrudButtons();
    }

    protected function setupDownloadRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{export}/download', [
            'as' => $routeName.'.download',
            'uses' => $controller.'@download',
            'operation' => 'download',
        ]);
    }

    protected function setupListOperation()
    {
        CRUD::column('user_id')->label(__('backpack-async-export::export.columns.user_id'));
        CRUD::column('export_type_name')->label(__('backpack-async-export::export.columns.export_type'));
        CRUD::column('filename')->label(__('backpack-async-export::export.columns.filename'));
        CRUD::column('status')->label(__('backpack-async-export::export.columns.status'));
        CRUD::column('error')->label(__('backpack-async-export::export.columns.error'));
        CRUD::column('completed_at')->label(__('backpack-async-export::export.columns.completed_at'));
    }

    public function download(Export $export): StreamedResponse
    {
        if (! $export->isReady) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $fileContent = file_get_contents($export->storagePath);
        $mimetype = File::mimeType($export->storagePath);
        $fileNameExplode = explode('/', $export->{Export::COLUMN_FILENAME});

        return response()->streamDownload(
            function () use ($fileContent): void {
                echo $fileContent;
            },
            end($fileNameExplode),
            [
                'Content-Type: ' . $mimetype,
            ]
        );
    }

    private function addCrudButtons(): void
    {
        $this->crud->addButtonFromModelFunction('line', 'download', 'getDownloadButton');
    }
}
