<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('options', 'App\Rules\Options@passes');
        \Validator::extend('minscale', 'App\Rules\MinScale@passes');
        \Validator::extend('maxscale', 'App\Rules\MaxScale@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
