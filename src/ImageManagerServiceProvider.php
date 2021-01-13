<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use Illuminate\Support\ServiceProvider;

class ImageManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services...
     *
     * @return void
     */
    public function boot()
    {
        $configPath = $this->configPath();

        $this->publishes([
            $configPath . '/imagemanager.php' => $this->publishPath('imagemanager.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    protected function configPath()
    {
        return __DIR__ . '/../config';
    }

    protected function publishPath($configFile)
    {
        if (function_exists('config_path')) {
            return config_path($configFile);
        } else {
            return base_path('config/' . $configFile);
        }
    }
}
