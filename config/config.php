<?php

return [
    /*
     * The transport type to use for transporting the responses.
     * Accepted options; cache or redis. Redis recommended for production.
     */
    'transport' => env('JOB_RESPONSE_TRANSPORT','redis'),

    /*
     * Set the connection to be used for redis (null uses Laravel default).
     */
    'redis' => [
        'connection' => null
    ],

    /*
     * When using the cache transport driver, the store that should be used (null uses Laravel default).
     */
    'cache' => [
        'store' => null
    ]
];
