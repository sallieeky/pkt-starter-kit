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
            Console\MakeBlankPageCommand\MakeVueBlankPageCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
            Console\MakeDatabaseTableCommand\MakeDatabaseTableCommand::class,
            Console\MakeComponentCommand\MakeComponentCommand::class,
            Console\MakeWidgetCommand\MakeWidgetCommand::class,
            Console\MediaCommand\InitMediaCommand::class,
            Console\MakeMigrationCommand\MakeMigrationCommand::class,
            Console\MailCommand\InitMailCommand::class,
            Console\MailCommand\MakeMailCommand::class,
            Console\StarterEnvironmentDecryptCommand::class,
            Console\MakeModelFromExistingTableCommand\MakeModelFromExistingTableCommand::class,
            // Console\RegenerateEncryptedDataCommand::class,
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
            Console\MakeBlankPageCommand\MakeVueBlankPageCommand::class,
            Console\MakeViewCommand\MakeViewCommand::class,
            Console\MakeDatabaseTableCommand\MakeDatabaseTableCommand::class,
            Console\MakeComponentCommand\MakeComponentCommand::class,
            Console\MakeWidgetCommand\MakeWidgetCommand::class,
            Console\MediaCommand\InitMediaCommand::class,
            Console\MakeMigrationCommand\MakeMigrationCommand::class,
            Console\MailCommand\InitMailCommand::class,
            Console\MailCommand\MakeMailCommand::class,
            Console\StarterEnvironmentDecryptCommand::class,
            Console\MakeModelFromExistingTableCommand\MakeModelFromExistingTableCommand::class,
            // Console\RegenerateEncryptedDataCommand::class,
        ];
    }
}
