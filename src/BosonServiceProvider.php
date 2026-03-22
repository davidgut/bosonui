<?php

namespace DavidGut\Boson;

use DavidGut\Boson\Commands\GenerateRulesCommand;
use Illuminate\Support\ServiceProvider;

class BosonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'boson');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/boson'),
        ], 'boson-views');

        $this->publishes([
            __DIR__.'/../config/boson.php' => config_path('boson.php'),
        ], 'boson-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRulesCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/boson.php', 'boson'
        );
    }
}

