<?php

namespace Insense\LaravelTelescopePruning;

use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;
use Insense\LaravelTelescopePruning\Commands\TrimCommand;

class TelescopePruningServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->registerCommands();
            return;
        }
        
        if($this->app->config->get('telescope-pruning.every_request_pruning', true)) {
            $this->app->terminating(function () {
                Telescope::withoutRecording(function() {
                    (new PruneEntries($this->app))->prune();
                });
            });            
        }
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

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../config/telescope-pruning.php' => config_path('telescope-pruning.php'),
        ], 'telescope-pruning-config');
    }
    
    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands() {
        $this->commands([
            TrimCommand::class,
        ]); 
    }
}
