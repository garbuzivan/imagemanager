<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use Illuminate\Support\ServiceProvider;

class ImageManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = $this->configPath();

        $this->publishes([
            $configPath . '/imagemanager.php' => $this->publishPath('imagemanager.php'),
        ], 'config');
    }

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
