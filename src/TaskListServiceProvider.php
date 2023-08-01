<?php

namespace Xguard\Tasklist;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Xguard\Tasklist\AWSStorage\S3Storage;
use Xguard\Tasklist\Commands\CreateAdmin;
use Xguard\Tasklist\Commands\TaskDeadlineHandler;
use Xguard\Tasklist\Http\Middleware\CheckHasAccess;
use Xguard\Tasklist\Http\Middleware\IsAdmin;

class TaskListServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws BindingResolutionException
     */

    public function register()
    {
        $this->app->make('Xguard\Tasklist\Http\Controllers\AppController');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'Xguard\Tasklist');
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'tasklist');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        app('router')->aliasMiddleware('tasklist_role_check', CheckHasAccess::class);
        app('router')->aliasMiddleware('tasklist_admin_check', isAdmin::class);
        $this->loadMigrationsFrom(__DIR__.'/Http/Middleware');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/database/seeds');
        $this->loadFactoriesFrom(__DIR__.'/database/factories');


        $this->commands([CreateAdmin::class, TaskDeadlineHandler::class]);

        include __DIR__.'/routes/web.php';
        include __DIR__.'/routes/api.php';

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/tasklist'),
        ], 'tasklist-assets');

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command(TaskDeadlineHandler::class)->cron('5-59/15 * * * *');
        });
        $this->app->singleton(S3Storage::class, function () {
            return Storage::disk('tasklist-s3');
        });
    }
}
