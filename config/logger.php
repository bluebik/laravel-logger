<?php

return [
    'logstash' => [
        'enabled' => env('LOGSTASH_ENABLED', false),
        'system_name' => env('LOGSTASH_SYSTEM_NAME'),
        'host' => env('LOGSTASH_HOST'),
        'port' => env('LOGSTASH_PORT'),
    ],
    'hidden_fields' => [
        'all' => [
            'password',
        ],
    ],
];