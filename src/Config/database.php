<?php
return [
    'connection' => [
        'elasticsearch' => [
            'hosts' => explode(',', env('ES_HOSTS'))
        ]
    ]
];
