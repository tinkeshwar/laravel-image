<?php

namespace Tinkeshwar\Imager;

use Illuminate\Support\ServiceProvider;
use Tinkeshwar\Imager\Console\ClearCache;
use Tinkeshwar\Imager\Console\InstallImagerPackage;

class ImagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('imager', function ($app) {
            return new Imager();
        });
        $this->app->make('Tinkeshwar\Imager\Http\Controllers\ImagerController');
        $this->app->make('config')['image'] ?? [];
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../routes/routes.php';
        include __DIR__ . '/../helper/imager.php';
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/image.php' => config_path('image.php'),
            ], 'config');
            $this->commands([
                InstallImagerPackage::class,
            ]);
            $this->commands([
                ClearCache::class,
            ]);
        }
    }
}
