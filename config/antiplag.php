<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Paramètres de détection de plagiat
    |--------------------------------------------------------------------------
    |
    | Configuration des seuils et paramètres pour la détection de similarité
    | et les règles de validation des documents.
    |
    */

    // Taille des shingles (n-grammes de mots)
    'shingle_size' => env('ANTIPLAG_SHINGLE_SIZE', 5),

    // Nombre de fonctions de hachage MinHash
    'minhash_functions' => env('ANTIPLAG_MINHASH_FUNCTIONS', 64),

    // Seuil de préfiltre MinHash (0.0 à 1.0)
    // Documents avec similarité MinHash >= ce seuil seront comparés en détail
    'minhash_threshold' => env('ANTIPLAG_MINHASH_THRESHOLD', 0.4),

    // Seuil de détection Jaccard pour upload automatique (0.0 à 1.0)
    'jaccard_threshold_upload' => env('ANTIPLAG_JACCARD_UPLOAD', 0.5),

    // Seuil de détection Jaccard pour analyse manuelle (0.0 à 1.0)
    'jaccard_threshold_analysis' => env('ANTIPLAG_JACCARD_ANALYSIS', 0.6),

    // Longueur minimale en mots
    'min_word_count' => env('ANTIPLAG_MIN_WORDS', 20),

    // Expressions bannies (phrases interdites)
    'banned_phrases' => [
        'loremipsum',
        'plagiarize_example',
        'test_plagiat',
        // Ajoutez d'autres expressions ici
    ],

    // Types de fichiers supportés pour l'extraction
    'supported_extensions' => [
        'pdf' => true,
        'doc' => true,
        'docx' => true,
        'txt' => true,
        'md' => true,
        'html' => true,
        'htm' => true,
        'csv' => true,
    ],

    // Taille maximale de fichier (en KB)
    'max_file_size' => env('ANTIPLAG_MAX_SIZE', 10240),

];