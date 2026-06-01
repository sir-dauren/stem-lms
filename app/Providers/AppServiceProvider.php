<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useTailwind();

        // Share dynamic header/footer menus + brand settings with every view.
        View::composer('*', function ($view) {
            $view->with('headerMenu', Menu::with('items.children.children')->where('location', 'header')->first());
            $view->with('footerMenu', Menu::with('items.children')->where('location', 'footer')->first());
            $view->with('brandName', setting('site_name', config('app.name')));
        });
    }
}
