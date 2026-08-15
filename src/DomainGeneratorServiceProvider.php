<?php

namespace Domain\DomainGenerator;

use Domain\DomainGenerator\Commands\CreateDomainStructureCommand;
use Illuminate\Support\ServiceProvider;

class DomainGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/domain-generator.php',
            'domain-generator'
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(
            __DIR__ . '/../routes/api.php'
        );

        $this->publishes([
            __DIR__ . '/../config/domain-generator.php' =>
                config_path('domain-generator.php'),
        ], 'domain-generator-config');

        if ($this->app->runningInConsole()) {

            $this->commands([
                CreateDomainStructureCommand::class,
            ]);

        }
    }
}