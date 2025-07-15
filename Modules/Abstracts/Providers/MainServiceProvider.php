<?php

namespace Modules\Abstracts\Providers;

use Illuminate\Support\ServiceProvider as LaravelAppServiceProvider;

class MainServiceProvider extends LaravelAppServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {

        // Register route service providers for each module
        // Add more modules as needed
        $this->registerRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // load other service providers
        // in main service provider
        $this->registerMigrations();
    }
    private function registerRoutes()
    {
        // Define the modules to register
        $modules = ['Admin', 'Customer', 'Owner'];

        foreach ($modules as $module) {
            $directories = glob("Modules/{$module}/*", GLOB_ONLYDIR);
            foreach ($directories as $directory) {
                // Construct the RouteServiceProvider class name
                $routeServiceProviderClass = str_replace('/', '\\', $directory) . '\Providers\RouteServiceProvider';
                // Register the class if it exists
                if (class_exists($routeServiceProviderClass)) {
                    $this->app->register($routeServiceProviderClass);
                }
            }
        }
    }



    private function registerMigrations()
    {
        // Register migrations for each module
        $modules = ['Admin', 'Customer', 'Owner'];

        foreach ($modules as $module) {
            $directories = glob("Modules/{$module}/*", GLOB_ONLYDIR);
            foreach ($directories as $directory) {
                $migrationPath = $directory . '/Database/Migrations'; 
                // Check if the migration path exists
                if (is_dir($migrationPath)) {
                    $this->loadMigrationsFrom($migrationPath);
                }
            }
        }
    }
}
