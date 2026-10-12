<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);
        \Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'svg', 'jpeg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );
            if (!in_array($format, $allow)) {
                return false;
            }
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Debug-only tooling, registered behind explicit flags instead of
        // unconditionally in config/app.php. In production
        // (composer install --no-dev) barryvdh/laravel-debugbar is absent, so
        // referencing its provider unconditionally would abort boot.
        if (config('telescope.enabled') && class_exists(\App\Providers\TelescopeServiceProvider::class)) {
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }

        if (config('debugbar.enabled') && class_exists(\Barryvdh\Debugbar\ServiceProvider::class)) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
