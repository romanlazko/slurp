<?php

namespace Romanlazko\Slurp\App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

class SlurpServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/slurp.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'slurp');
        // $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Blade::componentNamespace('Romanlazko\\Slurp\\App\\Views\\Components', 'slurp');

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/'),
            __DIR__.'/../../database/migrations/add_comment_column_to_permissions_table.php.stub' => $this->getMigrationFileName('add_comment_column_to_permissions_table.php'),
            __DIR__.'/../../database/seeders/SuperDuperAdminSeeder.php.stub' => database_path('seeders/SuperDuperAdminSeeder.php'),
        ], 'slurp');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
