<?php

return [
    /**************
     * MODEL
     ***************
     */
    'user_model' => 'App\Models\User',
    'export_model' => \Thomascombe\BackpackAsyncExport\Models\Export::class,
    /**************
     * Routing
     ***************
     */
    'admin_route' => 'export',
    /**************
     * Filesystem
     ***************
     */
    'export_memory_limit' => '2048M',
    'disk' => 'local',
];
