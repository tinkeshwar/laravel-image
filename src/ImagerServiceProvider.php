<?php

namespace Tinkeshwar\Imager;

use Illuminate\Support\ServiceProvider;

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
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/image.php' => config_path('image.php'),
            ]);
        }
    }
}
