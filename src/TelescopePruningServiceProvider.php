<?php

namespace Insense\LaravelTelescopePruning;

use Illuminate\Support\ServiceProvider;

class TelescopePruningServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            return;
        }
        
        $this->app->terminating(function () {
            (new PruneEntries($this->app))->prune();
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/telescope-pruning.php',
            'telescope-pruning'
        );
    }
}
