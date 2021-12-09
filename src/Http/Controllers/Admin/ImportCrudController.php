<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Thomascombe\BackpackAsyncExport\Enums\ActionType;

/**
 * Class ImportCrudController
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ImportCrudController extends CrudController
{
    use ListOperation;

    public function setup()
    {
        CRUD::setModel(config('backpack-async-export.export_model'));
        CRUD::setRoute(sprintf(
            '%s/%s',
            config('backpack.base.route_prefix'),
            config('backpack-async-export.admin_import_route')
        ));
        CRUD::setEntityNameStrings(
            __('backpack-async-export::import.name.singular'),
            __('backpack-async-export::import.name.plurial')
        );
        $this->crud->query->where('action_type', ActionType::Import);
    }

    protected function setupListOperation()
    {
        CRUD::column('user_id')->label(__('backpack-async-export::import.columns.user_id'));
        CRUD::column('export_type_name')->label(__('backpack-async-export::import.columns.export_type'));
        CRUD::column('filename')->label(__('backpack-async-export::import.columns.filename'));
        CRUD::column('status')->label(__('backpack-async-export::import.columns.status'));
        CRUD::column('error')->label(__('backpack-async-export::import.columns.error'));
        CRUD::column('completed_at')->label(__('backpack-async-export::import.columns.completed_at'));
    }
}
