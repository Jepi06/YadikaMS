<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\SPMB\Jurusan;

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
        Model::preventLazyLoading(!app()->isProduction());
        Paginator::useBootstrapFive();

        // Sidebar per-jurusan di panel admin SPMB selalu tersedia,
        // tanpa perlu di-pass manual dari setiap controller.
        View::composer('spmb.layouts.admin', function ($view) {
            $view->with('sidebarJurusan', Jurusan::orderBy('nama')->withCount('pendaftar')->get());
        });
    }
}
