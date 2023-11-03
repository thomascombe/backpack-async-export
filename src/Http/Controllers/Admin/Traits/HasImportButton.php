<?php

namespace Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Prologue\Alerts\Facades\Alert;
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ImportableCrud;
use Thomascombe\BackpackAsyncExport\Http\Requests\ImportRequest;
use Thomascombe\BackpackAsyncExport\Jobs\ImportJob;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;
use Throwable;

/**
 * @mixin CrudController
 * @mixin ImportableCrud
 */
trait HasImportButton
{
    /**
     * @throws Throwable
     */
    public function import(): View
    {
        $this->checkImportInterfaceImplementation();

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle();

        return view('backpack-async-export::pages.import', $this->data);
    }

    protected function getImportParametersMethodName(): string
    {
        return 'getImportParameters';
    }

    /**
     * @throws Throwable
     */
    public function importSubmit(ImportRequest $request): RedirectResponse
    {
        $this->checkImportInterfaceImplementation();

        /** @var ImportExport $exportModel */
        $exportModel = $this->{$this->getImportMethodName()}();
        $parameters = $this->{$this->getImportParametersMethodName()}();

        list($mimetypeState, $validator) = $this->checkFileMimetype($request);
        if (!$mimetypeState) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->saveUploadFile($request, $exportModel);

        ImportJob::dispatch($exportModel, ...$parameters);
        if (config('queue.default') !== 'sync') {
            Alert::info(__('backpack-async-export::import.notifications.queued'))->flash();
        } else {
            Alert::warning(__('backpack-async-export::import.notifications.sync'))->flash();
        }

        return response()->redirectToRoute(config('backpack-async-import-export.admin_import_route') . '.index');
    }

    protected function getImportMethodName(): string
    {
        return 'getImport';
    }

    private function checkFileMimetype(ImportRequest $request): array
    {
        $parameters = $this->{$this->getImportParametersMethodName()}();
        $mimetypes = isset($parameters['private']) ? $parameters['private']['mimetypes'] : [];

        if (!empty($mimetypes)) {
            $validator = Validator::make($request->all(), [
                $request::PARAM_FILE => [
                    'required',
                    'file',
                    sprintf('mimetypes:%s', implode(',', $mimetypes)),
                ],
            ]);

            if ($validator->fails()) {
                Alert::error($validator->errors()->get('file'))->flash();

                return [false, $validator];
            }

            return [true, $validator];
        }

        return [true];
    }

    private function saveUploadFile(ImportRequest $request, ImportExport $exportModel): void
    {
        /** @var UploadedFile $file */
        $file = $request->files->get($request::PARAM_FILE);
        $filename = sprintf('%s/%s', $exportModel->{ImportExport::COLUMN_FILENAME}, $file->getClientOriginalName());
        Storage::disk($exportModel->{ImportExport::COLUMN_DISK})
            ->put(
                $filename,
                $file->getContent()
            );
        $exportModel->{ImportExport::COLUMN_FILENAME} = $filename;
        $exportModel->save();
    }

    /**
     * @throws Exception
     */
    protected function setupImportDefaults(): void
    {
        $this->crud->allowAccess('import');

        $this->crud->operation('list', function () {
            $this->addImportButtons();
        });

        $this->crud->operation('import', function () {
            $parameters = $this->{$this->getImportParametersMethodName()}();

            $this->crud->field([
                'name' => 'file',
                'label' => trans('backpack-async-export::admin.column.file'),
                'type' => 'upload',
                'upload' => true,
                'disk' => 'local',
                'hint' => $parameters['private']['hint'] ?? null,
                'attributes' => [
                    'accept' => implode(
                        ',',
                        $parameters['private']['mimetypes'] ?? []
                    ),
                ],
            ]);

            $this->crud->enableGroupedErrors();
        });
    }

    /**
     * @throws Throwable
     */
    protected function addImportButtons(): void
    {
        $this->checkImportInterfaceImplementation();

        $this->crud->setting(
            'import_route',
            $this->crud->getRoute() . '/' . config('backpack-async-import-export.admin_import_route')
        );
        $this->crud->addButton('top', 'import', 'view', 'backpack-async-export::buttons/import', 'end');
    }

    /**
     * @throws Throwable
     */
    protected function checkImportInterfaceImplementation(): void
    {
        throw_unless(
            $this instanceof ImportableCrud,
            new Exception(sprintf('%s need to implement %s', self::class, ImportableCrud::class))
        );
    }

    /**
     * @throws Throwable
     */
    protected function setupImportRoutes($segment, $routeName, $controller): void
    {
        $this->checkImportInterfaceImplementation();

        Route::get($segment . '/' . config('backpack-async-import-export.admin_import_route'), [
            'as' => $routeName . '.import',
            'uses' => $controller . '@import',
            'operation' => 'import',
        ]);
        Route::post($segment . '/' . config('backpack-async-import-export.admin_import_route'), [
            'as' => $routeName . '.import-submit',
            'uses' => $controller . '@importSubmit',
            'operation' => 'import-submit',
        ]);
    }
}
