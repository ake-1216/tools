<?php

namespace Ake\Tools;

use Ake\Tools\Commands\DcatMake;
use Ake\Tools\Commands\ExportSql;
use Ake\Tools\Commands\GeneratePermission;
use Ake\Tools\Commands\Service;
use Illuminate\Support\ServiceProvider;

class AkeServiceProvider extends ServiceProvider
{
    protected $commands = [
        Service::class,
        GeneratePermission::class,
        DcatMake::class,
        ExportSql::class,
    ];

    public function register()
    {
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->registerPublishing();
        $this->registerLoad();
        $this->registerWechat();
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

    private function registerWechat()
    {
        $this->publishes([
            __DIR__ . '/config/Wechat.php' => config_path('Wechat.php'),
            __DIR__ . '/database/migration/add_wechat_field.php' => database_path('/migration/add_wechat_field.php'),
        ], 'ake-wechat');
    }

    private function publishImage()
    {
        $this->publishes([
            __DIR__ . '/database/migration/create_images_table.php' => database_path('/migration/'. date('Y_m_d_') .'create_images_table.php'),
        ], 'ake-image');
    }
}