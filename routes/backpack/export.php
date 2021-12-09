<?php
Route::group([
    'namespace'  => 'Thomascombe\BackpackAsyncExport\Http\Controllers\Admin',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {
    if (config('backpack-async-export.feature_enabled.export')) {
        Route::crud(config('backpack-async-export.admin_export_route'), 'ExportCrudController');
    }
    if (config('backpack-async-export.feature_enabled.import')) {
        Route::crud(config('backpack-async-export.admin_import_route'), 'ImportCrudController');
    }
});
