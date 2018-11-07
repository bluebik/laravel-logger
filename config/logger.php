<?php

return [
    'logstash' => [
        'enabled' => env('LOGSTASH_ENABLED', false),
        'system_name' => env('LOGSTASH_SYSTEM_NAME'),
        'host' => env('LOGSTASH_HOST'),
        'port' => env('LOGSTASH_PORT'),
        'protocol' => env('LOGSTASH_PROTOCOL'),
        'exclude_names' => explode(',', env('LOGSTASH_EXCLUDE_NAMES', '')),
    ],
    'hidden_fields' => [
        'all' => [
            'password',
        ],
    ],
];