# Laravel Performance package

This performance package (currently) contains two configurable performance related features which you can use to identify potential slow parts in your application:
- Slow query logging
- Force eager loading (use in development!)


## Include the package

For dev-only use:

`composer require --dev wedesignit/laravel-performance`

For using it on other environments too:

`composer require wedesignit/laravel-performance`


## Publishing the config

`php artisan vendor:publish --provider="WeDesignIt\LaravelPerformance\Providers\LaravelPerformanceServiceProvider" --tag="config"`


## Slow query logging

### config

The package can log slow queries. To do this, either 
- enable this in the config or 
- set an environment key `ENABLE_SLOW_QUERY_LOGGING=true`

Then, configure the threshold: this is the amount of milliseconds after which you 
consider a query to be "slow"

### logging

The package will look for a `query` channel for logging. You can create your own channel called `query` in `config/logging.php` to define where you want the slow queries to be logged.
If you don't define it, the `query` channel will be temporarily created using the following config:

```php
[
    'driver' => 'daily',
    'path'   => storage_path('logs/query.log'),
    'level'  => env('LOG_LEVEL', 'debug'),
    'days'   => 2,
]
```

This means: by default the slow queries will be logged in a `query.log` file prefixed with date (due to the `daily` driver) and kept for 2 days, in the (default) logs folder `storage/app/logs`.

### what's being logged

The following details are logged per entry:
- SQL
- Query bindings
- Time took
- The file with the code that triggered the query
- The line in the file on which the code is


## Force eager loading

Eager loading relationships can greatly speed up your application by eliminating subsequent queries. By enabling forced eager loading, an exception will be thrown when a relationship is not eager loaded so you can decide if it's right to eager load that relationship.

Note: forced eager loading will NOT be enabled in production!

### config

To enable this feature:
- enable this in the config or
- set an environment key `ENABLE_FORCED_EAGER_LOADING=true`

After this, pages or code which use a not-eager-loaded relationship will throw an Exception telling you which relationship wasn't eager loaded on which model.

### UnEagerLoadableRelations

Some relationships should not be eager loaded, but would also yield an Exception according to the above setting.
If a model contains one or more relationships that cannot or should not be eager loaded, use the `ContainsUnEagerLoadableRelations` trait.

Then, use a property `array $without` on the Model(s) using the trait to prevent the relations in the `$without` from throwing eager loading exceptions.

```php
<?php

use WeDesignIt\LaravelPerformance\Traits\ContainsUnEagerLoadableRelations;

class Car {

    use ContainsUnEagerLoadableRelations;

    protected $with = [
        'windows', 'lights',
    ];

    protected array $without = [
        'wheels',
    ];
}
```
