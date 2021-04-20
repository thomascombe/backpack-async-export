<?php
Route::group([
    'namespace'  => 'Thomascombe\BackpackAsyncExport\Http\Controllers\Admin',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {
    Route::crud(config('backpack_async_export.admin_route'), 'ExportCrudController');
});
