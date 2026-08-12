<?php

namespace Domain\DomainGenerator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DomainGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateDomainStructureCommand::class,
            ]);
        }
        
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'api/auth',
            'middleware' => 'api',
        ], function () {
            Route::post('login', [\Domain\DomainGenerator\Http\Controllers\AuthController::class, 'login']);
            Route::post('logout', [\Domain\DomainGenerator\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:api');
            Route::post('refresh', [\Domain\DomainGenerator\Http\Controllers\AuthController::class, 'refresh'])->middleware('auth:api');
            Route::get('me', [\Domain\DomainGenerator\Http\Controllers\AuthController::class, 'me'])->middleware('auth:api');
        });
    }
}
