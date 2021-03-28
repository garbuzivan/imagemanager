<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use GarbuzIvan\ImageManager\Laravel\Config;
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
        $this->app->singleton(Config::class, function ($app) {
            return new Config;
        });
        $this->app->singleton(ImageManager::class, function ($app) {
            return new ImageManager(app(Config::class));
        });
    }

    /**
     * @return string
     */
    protected function configPath(): string
    {
        return __DIR__ . '/../config';
    }

    /**
     * @param $configFile
     * @return string
     */
    protected function publishPath($configFile): string
    {
        if (function_exists('config_path')) {
            return config_path($configFile);
        } else {
            return base_path('config/' . $configFile);
        }
    }
}
