<?php

return [
    'name' => [
        'singular' => 'import',
        'plurial' => 'imports',
    ],
    'columns' => [
        'user_id' => 'Utilisateur',
        'export_type' => 'Type',
        'filename' => 'Nom du fichier',
        'status' => 'Statut',
        'error' => 'Erreur',
        'completed_at' => 'Terminé le',
    ],
    'buttons' => [
        'import' => 'Importer',
    ],
    'notifications' => [
        'queued' => 'Import lancé ! Les résultats seront disponible dans l\'onglet "Import" lorsque ce sera prêt.',
        'sync' => 'Import lancé en synchrone ! Les résultats sont disponibles dans l\'onglet "Import".',
    ],
    'errors' => [
        'global-import' => 'Erreur pendant l\'import',
    ],
];
