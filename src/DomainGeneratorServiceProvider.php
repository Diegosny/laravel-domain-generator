<?php

namespace SeuUsuario\DomainGenerator;

use Illuminate\Support\ServiceProvider;
use SeuUsuario\DomainGenerator\Commands\CreateDomainStructureCommand;

class DomainGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateDomainStructureCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        //
    }
}