<?php

namespace Pkt\StarterKit;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StarterKitServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
            Console\MakeResourceCommand\MakeResourceCommand::class,
            Console\LeaderCommand\InitLeaderCommand::class,
            Console\LeaderCommand\SyncLeaderCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Console\InstallCommand::class,
            Console\MakeResourceCommand\MakeResourceCommand::class,
            Console\LeaderCommand\InitLeaderCommand::class,
            Console\LeaderCommand\SyncLeaderCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
        ];
    }
}
