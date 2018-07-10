<?php

namespace App\Providers;

use App\Group;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view)
        {
            $groups = Group::get();
            $languages = Language::get();

            $view->with(['groups'=> $groups, 'languages'=> $languages]);
        });
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