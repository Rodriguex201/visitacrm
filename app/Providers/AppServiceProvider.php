<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();

            $brandTitle = "VisitaCRM";
            $brandSubtitle = "Gestión comercial";

            if ($user && $user->tipo_usuario !== "administracion") {
                $brandTitle = $user->name ?: "Usuario";
                $brandSubtitle = $user->codigo ?: "S/C";
            }

            $view->with([
                "sidebarBrandTitle" => $brandTitle,
                "sidebarBrandSubtitle" => $brandSubtitle,
            ]);
        });
    }
}
