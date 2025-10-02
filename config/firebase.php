<?php

return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/firebase-credentials.json')),
    ],
    
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],
];