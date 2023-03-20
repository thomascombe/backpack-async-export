<?php

return [
    'name' => [
        'singular' => 'import',
        'plurial' => 'imports',
    ],
    'columns' => [
        'user_id' => 'User',
        'export_type' => 'Type',
        'filename' => 'Filename',
        'status' => 'Status',
        'error' => 'Error',
        'completed_at' => 'Completed at',
    ],
    'buttons' => [
        'import' => 'Import',
    ],
    'notifications' => [
        'queued' => 'Import launched! Results will be available when ready on "Import" tab',
        'sync' => 'Import launched in sync! Results is available on "Import" tab',
    ],
    'errors' => [
        'global-import' => 'Error during import',
    ],
];
