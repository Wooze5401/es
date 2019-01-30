<?php
return [
    'es' => [
        'driver' => 'daily',
        'path' => storage_path(env('ES_LOG', 'logs/es/es.log')),
        'level' => 'debug',
    ],
];
