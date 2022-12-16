<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Slow query logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable slow query logging to log warnings about slow queries
    | exceeding the threshold.
    |
    */
    'slow_query_logging'   => [
        'enabled'   => env('ENABLE_SLOW_QUERY_LOGGING', false),

        // Threshold in milliseconds
        'threshold' => env('SLOW_QUERY_LOGGING_THRESHOLD', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Forced eager loading
    |--------------------------------------------------------------------------
    |
    | Enable or disable forced eager loading to prevent lazy loading of models.
    | Note that this only applies to non-production environments as you don't
    | want to throw exceptions in production.
    |
    */
    'forced_eager_loading' => [
        'enabled' => env('ENABLE_FORCED_EAGER_LOADING', false),
    ],

];
