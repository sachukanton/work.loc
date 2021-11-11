<?php

namespace Kolirt\Frontpad;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $commands = [
        Commands\InstallCommand::class
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/frontpad.php', 'frontpad');

        $this->publishes([
            __DIR__ . '/../config/frontpad.php' => config_path('frontpad.php')
        ]);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->commands($this->commands);

        app()->bind('frontpad', function () {
            return new Frontpad();
        });
    }
}