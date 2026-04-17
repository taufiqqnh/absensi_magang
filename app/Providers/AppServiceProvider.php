<?php

namespace App\Providers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
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
        Carbon::setLocale('id');

         // Mengirim semua kategori ke sidebar view secara global
        // View::composer('components.admin.sidebar', function ($view) {
        //     $categories = Category::all();
        //     $view->with('categories', $categories);
        // });
    }
}
