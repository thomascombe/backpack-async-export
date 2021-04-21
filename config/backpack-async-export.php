<?php

return [
    'user_model' => 'App\Models\User',
    'export_model' => \Thomascombe\BackpackAsyncExport\Models\Export::class,
    'admin_route' => 'export',
    'export_memory_limit' => '2048M',
];
