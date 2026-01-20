<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\OperationType;
use App\Models\User;
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
         View::composer('*', function ($view) {
        if (auth()->check()) {
            $companyId = session('company_id') ?? auth()->User()->company_id;
            $operationTypes = OperationType::where('company_id', $companyId)->get();
            $view->with('operationTypes', $operationTypes);
        }
    });
    }
}
