<?php

namespace App\Providers;

use App\Models\Empresa;
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
                $mainEmpresa = Empresa::query()
                    ->where('user_id', $user->id)
                    ->orderBy('id', "asc")
                    ->first();

                $brandTitle = $mainEmpresa?->nombre ?: "Mis empresas";
                $brandSubtitle = $user->codigo ?: "S/C";
            }

            $view->with([
                "sidebarBrandTitle" => $brandTitle,
                "sidebarBrandSubtitle" => $brandSubtitle,
            ]);
        });
    }
}
