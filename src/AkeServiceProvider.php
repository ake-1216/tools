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
}