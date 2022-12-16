<?php

namespace WeDesignIt\LaravelPerformance\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class LaravelPerformanceServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/performance.php' => config_path('performance.php'),
        ], 'config');

        // ---------------------------------------------------------------------
        // Slow query logging
        // ---------------------------------------------------------------------
        if (config('performance.slow_query_logging.enabled', false)) {
            // check if the channel was already created in the app config
            if (!($this->app->make('config')->has('logging.channels.query'))) {
                // if not, use this as a fallback
                $this->app->make('config')->set('logging.channels.query', [
                    'driver' => 'daily',
                    'path'   => storage_path('logs/query.log'),
                    'level'  => env('LOG_LEVEL', 'debug'),
                    'days'   => 2,
                ]);
            }

            $this->registerSlowQueryLogging();
        }

        // ---------------------------------------------------------------------
        // Eager load optimization
        // ---------------------------------------------------------------------
        if (config('performance.forced_eager_loading.enabled', false) && ! app()->isProduction()) {
            Model::preventLazyLoading(!app()->isProduction());

            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                if (!$this->preventsLazyLoadingRelation($model, $relation)) {
                    throw new LazyLoadingViolationException($model, $relation);
                }
            });
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/performance.php', 'performance'
        );
    }

    private function registerSlowQueryLogging()
    {
        $this->app['db']->listen(function ($query) {
            if ($query->time >= config(
                    'performance.slow_query_logging.threshold', 10000
                )) {
                $this->logSlowQuery($query);
            }
        });
    }

    private function logSlowQuery($query)
    {
        /** @var QueryExecuted $query */
        $location = collect(debug_backtrace())->filter(function ($trace) {
            return !Str::contains(data_get($trace, 'file', 'vendor/'), ['vendor/', 'packages/']);
        })->first(); // grab the first element of non vendor/ calls
        $bindings = implode(
            ", ",
            $query->bindings
        ); // format the bindings as string
        Log::channel('query')->warning("
        — — — — — —
        Sql: $query->sql
        Bindings: $bindings
        Time: $query->time
        File: ${location['file']}
        Line: ${location['line']}
        — — — — — —
        ");
    }

    private function preventsLazyLoadingRelation(Model $model, string $relation) : bool {
        $prevent = method_exists($model, 'withoutEagerLoading') ? $model->withoutEagerLoading() : [];

        return in_array($relation, $prevent);
    }
}
