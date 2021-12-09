<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ImportableCrud;
use Thomascombe\BackpackAsyncExport\Http\Requests\ImportRequest;
use Thomascombe\BackpackAsyncExport\Jobs\ImportJob;
use Thomascombe\BackpackAsyncExport\Models\Export;

/**
 * Trait HasImportButton
 * @package Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits
 *
 * @mixin CrudController
 * @mixin ImportableCrud
 */
trait HasImportButton
{

    /**
     * @throws \Exception
     */
    protected function addImportButtons()
    {
        $this->checkInterfaceImplementation();

        $this->crud->setting('import_route', url(route('user.import')));
        $this->crud->addButton('top', 'export', 'view', 'backpack-async-export::buttons/import', 'end');
    }

    /**
     * @throws \Exception
     */
    protected function setupImportRoutes($segment, $routeName, $controller)
    {
        $this->checkInterfaceImplementation();

        Route::get($segment . '/' . config('backpack-async-export.admin_import_route'), [
            'as' => $routeName . '.import',
            'uses' => $controller . '@import',
            'operation' => 'import',
        ]);
        Route::post($segment . '/' . config('backpack-async-export.admin_import_route'), [
            'as' => $routeName . '.import-submit',
            'uses' => $controller . '@importSubmit',
            'operation' => 'import-submit',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function import(): View
    {
        $this->checkInterfaceImplementation();

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle();
        $this->data['fields'] = [
            [
                'name' => 'file',
                'label' => trans('backpack-async-export::admin.column.file'),
                'type' => 'upload',
                'upload' => true,
                //                    'hint' => $hint,
                //                    'attributes' => [
                //                        'accept' => $accept,
                //                    ],
            ]
        ];

        return view('backpack-async-export::pages.import', $this->data);
    }

    /**
     * @throws \Exception
     */
    public function importSubmit(ImportRequest $request): RedirectResponse
    {
        $this->checkInterfaceImplementation();

        /** @var Export $exportModel */
        $exportModel = $this->{$this->getImportMethodName()}();
        $parameters = $this->{$this->getImportParametersMethodName()}();

        /** @var UploadedFile $file */
        $file = $request->files->get($request::PARAM_FILE);
        $filename = sprintf('%s/%s', $exportModel->{Export::COLUMN_FILENAME}, $file->getClientOriginalName());
        Storage::disk($exportModel->{Export::COLUMN_DISK})
            ->put(
                $filename,
                $file->getContent()
            );
        $exportModel->{Export::COLUMN_FILENAME} = $filename;
        $exportModel->save();
        ImportJob::dispatch($exportModel, ...$parameters);
        \Alert::info(__('backpack-async-export::import.notifications.queued'))->flash();

        return response()->redirectToRoute(config('backpack-async-export.admin_import_route') . '.index');
    }

    /**
     * @throws \Exception
     */
    protected function checkInterfaceImplementation(): void
    {
        if (!$this instanceof ImportableCrud) {
            throw new \Exception(sprintf('%s need to implement %s', self::class, ImportableCrud::class));
        }
    }

    protected function getImportMethodName(): string
    {
        return 'getImport';
    }

    protected function getImportParametersMethodName(): string
    {
        return 'getImportParameters';
    }
}
