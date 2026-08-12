<?php

namespace Domain\DomainGenerator;

use Domain\DomainGenerator\Commands\CreateDomainStructureCommand;
use Domain\DomainGenerator\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider as JwtServiceProvider;

class DomainGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(JwtServiceProvider::class);
    }

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
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
            Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
            Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
        });
    }
}
