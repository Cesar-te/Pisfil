<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $menus = \App\Models\Menu::whereNull('padre_id')
                ->with([
                    'permiso',
                    'submenus.permiso',
                    'submenus.submenus.permiso',
                    'submenus.submenus.submenus.permiso',
                ])
                ->orderBy('orden')
                ->get();
            $view->with('global_menus', $menus);
        });
    }
}
