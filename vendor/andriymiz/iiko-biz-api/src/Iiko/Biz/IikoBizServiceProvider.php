<?php

namespace Iiko\Biz;

use Illuminate\Support\ServiceProvider;
use Iiko\Biz\Client as IikoClient;

class IikoBizServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {  // echo"77777===".__DIR__.'/../../config/iiko-biz.php'."\r\n";
        $this->publishes([
            __DIR__.'/../../config/iiko-biz.php' => config_path('iiko-biz.php'),
        ], 'config');
        //echo"====888===\r\n";
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packageConfigFile = __DIR__.'/../../config/iiko-biz.php';

        $this->mergeConfigFrom(
            $packageConfigFile, 'iiko-biz'
        );

        $this->app->singleton('iiko', function () {
            return new IikoClient(config('iiko-biz'));
        });
    }
}
