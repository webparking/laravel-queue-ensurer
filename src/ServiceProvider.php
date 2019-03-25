<?php

namespace Webparking\QueueEnsurer;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Webparking\QueueEnsurer\Commands\EnsureProcesses;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/queue-ensurer.php',
            'queue-ensurer'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/queue-ensurer.php' => config_path('queue-ensurer.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                EnsureProcesses::class,
            ]);

            if (config('queue-ensurer.schedule')) {
                $this->app->booted(function () {
                    /** @var Schedule $schedule */
                    $schedule = app(Schedule::class);
                    $schedule->command(EnsureProcesses::class)->everyMinute();
                });
            }
        }
    }
}
