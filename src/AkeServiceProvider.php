<?php

namespace Ake\Tools;

use Ake\Tools\Commands\GeneratePermission;
use Ake\Tools\Commands\MakeService;
use Illuminate\Support\ServiceProvider;

class AkeServiceProvider extends ServiceProvider
{
    protected $commands = [
        MakeService::class,
        GeneratePermission::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->registerPublishing();
        $this->registerLoad();
    }

    private function registerPublishing()
    {
        #发布 stub 存根文件到根目录
        $this->publishes([__DIR__ . '/stubs/' => base_path('stubs/')], 'ake-tools-stub');
    }

    private function registerLoad()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ake-tools');
    }
}