<?php

namespace Pkt\StarterKit;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Schema\Blueprint;
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
            Console\MakeBlankPageCommand\MakeVueBlankPageCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
            Console\MakeDatabaseTableCommand\MakeDatabaseTableCommand::class,
            Console\MakeComponentCommand\MakeComponentCommand::class,
        ]);

        Blueprint::macro('createdUpdatedBy', function () {
            $this->foreignIdBy(config('auth.providers.users.model'), 'created_by')->nullable();
            $this->foreignIdBy(config('auth.providers.users.model'), 'updated_by')->nullable();
        });
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
            Console\MakeBlankPageCommand\MakeVueBlankPageCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
            Console\MakeDatabaseTableCommand\MakeDatabaseTableCommand::class,
            Console\MakeComponentCommand\MakeComponentCommand::class,
        ];
    }
}
