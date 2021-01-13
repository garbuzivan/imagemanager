<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class ImageManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        //$this->mergeConfigFrom(__DIR__ . '/../config/mediaimage.php', 'mediaimage');
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mediaimage.php' => config_path('imagemanager.php'),
            ], 'config');
        }
    }
}
