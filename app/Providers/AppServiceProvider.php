<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        ViewComposer::composer('user.profile-links', function (View $view) {
            $currentRouteName = Route::currentRouteName();

            $links = [

                [
                    'route'  => route('user.profile'),
                    'active' => $currentRouteName == 'user.profile',
                    'title'  => 'Profile',
                ],
                [
                    'route'  => route('user.suggestions'),
                    'active' => $currentRouteName == 'user.suggestions',
                    'title'  => 'Suggestion requests',
                ]

            ];
            $view->with('links', $links);
        });
    }
}
