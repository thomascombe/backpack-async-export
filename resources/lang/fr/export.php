<?php

return [
    'name' => [
        'singular' => 'export',
        'plurial' => 'exports',
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
        'exports' => 'Exporter',
        'download' => 'Télécharger',
    ],
    'notifications' => [
        'queued' => 'Export lancé ! Les résultats seront disponible dans l\'onglet "Export" lorsque ce sera prêt.',
        'sync' => 'Export lancé en synchrone ! Les résultats sont disponibles dans l\'onglet "Export".',
    ],
    'errors' => [
        'global-export' => 'Error during export',
    ],
];
