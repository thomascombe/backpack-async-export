<?php
Route::group([
    'namespace'  => 'Thomascombe\BackpackAsyncExport\Http\Controllers\Admin',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {
    if (config('backpack-async-import-export.feature_enabled.export')) {
        Route::crud(config('backpack-async-import-export.admin_export_route'), 'ExportCrudController');
    }
    if (config('backpack-async-import-export.feature_enabled.import')) {
        Route::crud(config('backpack-async-import-export.admin_import_route'), 'ImportCrudController');
    }
});
