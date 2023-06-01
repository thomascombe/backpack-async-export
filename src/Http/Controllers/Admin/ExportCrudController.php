<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

/**
 * Class ExportCrudController
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ExportCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    public function setup(): void
    {
        CRUD::setModel(config('backpack-async-import-export.import_export_model'));
        CRUD::setRoute(
            sprintf(
                '%s/%s',
                config('backpack.base.route_prefix'),
                config('backpack-async-import-export.admin_export_route')
            )
        );
        CRUD::setEntityNameStrings(
            __('backpack-async-export::export.name.singular'),
            __('backpack-async-export::export.name.plurial')
        );
        $this->crud->query->where('action_type', ActionType::Export->value);
        $this->addCrudButtons();
    }

    private function addCrudButtons(): void
    {
        $this->crud->addButtonFromModelFunction('line', 'download', 'getDownloadButton');
    }

    public function download(ImportExport $export): BinaryFileResponse
    {
        if (!$export->isReady) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $mimetype = File::mimeType($export->storagePath);
        $fileNameExplode = explode('/', $export->{ImportExport::COLUMN_FILENAME});

        return response()->download(
            $export->storagePath,
            end($fileNameExplode),
            [
                'Content-Type: ' . $mimetype,
            ]
        );
    }

    protected function setupDownloadRoutes(string $segment, string $routeName, string $controller): void
    {
        Route::get($segment . '/{export}/download', [
            'as' => $routeName . '.download',
            'uses' => $controller . '@download',
            'operation' => 'download',
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->setupListOperation();

        CRUD::column(ImportExport::COLUMN_ERROR)->limit(1000); // @phpstan-ignore-line
    }

    protected function setupListOperation(): void
    {
        CRUD::column('user_id')->label(__('backpack-async-export::export.columns.user_id'));
        CRUD::column('export_type_name')->label(__('backpack-async-export::export.columns.export_type'));
        CRUD::column('filename')->label(__('backpack-async-export::export.columns.filename'));
        CRUD::column('status')->type('enum')->label(__('backpack-async-export::export.columns.status'));
        CRUD::column('error')->label(__('backpack-async-export::export.columns.error'));
        CRUD::column('completed_at')->label(__('backpack-async-export::export.columns.completed_at'));
    }
}
