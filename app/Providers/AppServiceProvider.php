<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set view paths
        $this->loadViewsFrom(resource_path('views'), null);
        
        // G2Bulk API settings are loaded directly in the service
        // No need for runtime config loading
    }
}
