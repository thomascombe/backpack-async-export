<?php

return [
    /**************
     * GLOBAL
     ***************
     */
    'feature_enabled' => [
        'export' => true,
        'import' => true,
    ],
    /**************
     * MODEL
     ***************
     */
    'user_model' => 'App\Models\User',
    'import_export_model' => Thomascombe\BackpackAsyncExport\Models\ImportExport::class,
    /**************
     * Routing
     ***************
     */
    'admin_export_route' => 'export',
    'admin_import_route' => 'import',
    /**************
     * Filesystem
     ***************
     */
    'export_memory_limit' => '2048M',
    'disk' => 'local',
    /**************
     * Simple csv export
     ***************
     */
    'simple_csv_export' => [
        'queue' => 'export',
    ],
];
