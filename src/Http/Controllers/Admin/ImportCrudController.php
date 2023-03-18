<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

/**
 * Class ImportCrudController
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ImportCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    public function setup()
    {
        CRUD::setModel(config('backpack-async-import-export.import_export_model'));
        CRUD::setRoute(
            sprintf(
                '%s/%s',
                config('backpack.base.route_prefix'),
                config('backpack-async-import-export.admin_import_route')
            )
        );
        CRUD::setEntityNameStrings(
            __('backpack-async-export::import.name.singular'),
            __('backpack-async-export::import.name.plurial')
        );
        $this->crud->query->where('action_type', ActionType::Import->value);
    }

    protected function setupListOperation(): void
    {
        CRUD::column('user_id')->label(__('backpack-async-export::import.columns.user_id'));
        CRUD::column('export_type_name')->label(__('backpack-async-export::import.columns.export_type'));
        CRUD::column('filename')->label(__('backpack-async-export::import.columns.filename'));
        CRUD::column('status')->type('enum')->label(__('backpack-async-export::import.columns.status'));
        CRUD::column('error')->label(__('backpack-async-export::import.columns.error'));
        CRUD::column('completed_at')->label(__('backpack-async-export::import.columns.completed_at'));
    }

    protected function setupShowOperation(): void
    {
        $this->setupListOperation();

        CRUD::column(ImportExport::COLUMN_ERROR)->limit(1000); /* @psalm-suppress UndefinedMagicMethod */
    }
}
