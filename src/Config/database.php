<?php

return [
    'elasticsearch' => [
        'hosts' => explode(',', env('ES_HOSTS'))
    ]
];
