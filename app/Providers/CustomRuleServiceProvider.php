<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class CustomRuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('options', 'App\Rules\Options@passes');
        Validator::extend('minscale', 'App\Rules\MinScale@passes');
        Validator::extend('maxscale', 'App\Rules\MaxScale@passes');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
