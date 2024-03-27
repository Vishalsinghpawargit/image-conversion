<?php

namespace VishalPawar\ImageConvert;

use Illuminate\Support\ServiceProvider;

class ImageConvertServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        require_once(__DIR__.'/helper/ImageHelper.php');

        $this->mergeConfigFrom(
            __DIR__.'/config/ImageConvert.php', 'ImageConvert'
        );

        $this->publishes([
            __DIR__.'/config/ImageConvert.php' => config_path('ImageConvert.php'),
        ], "config");

    }

    public function register()
    {
        
    }

}