<?php

namespace App\Providers;

use App\Library\IdentifyDevice;
use App\Library\Wrap;
use App\Models\Shop\Basket;
use App\Models\User\Role;
use App\Models\User\User;
use App\Observers\RoleObservers;
use App\Observers\UserObservers;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('device', function ($app) {
            return new IdentifyDevice();
        });
        $this->app->singleton('wrap', function ($app) {
            return new Wrap(request(), $app);
        });
        $this->app->singleton('basket', function ($app) {
            return Basket::init();
        });

        require_once(app_path('Helpers/Define.php'));
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
        Role::observe(RoleObservers::class);
        User::observe(UserObservers::class);

        Validator::extend('reCaptchaV3', 'App\Validators\Validations@validateReCaptchaV3');
        Validator::extend('phoneNumber', 'App\Validators\Validations@validatePhoneNumber');
        Validator::extend('phoneOperatorCode', 'App\Validators\Validations@validatePhoneOperatorCode');
        Validator::extend('existsData', 'App\Validators\Validations@validateExistsDataInTable');
        Validator::extend('multiRequiredIf', 'App\Validators\Validations@validateMultiRequiredIf');

    }

}
