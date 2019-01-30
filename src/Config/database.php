<?php
return [
    'connections' => [
        'elasticsearch' => [
            'hosts' => explode(',', env('ES_HOSTS'))
        ],
    ],
];
