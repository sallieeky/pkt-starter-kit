<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallReactStack
{
    protected function installReactStack()
    {
        $this->components->info('Installing React stack...');

        // Call breeze
        $this->installBreezeIfNotExist();
        $this->runCommands(['php artisan breeze:install react']);
        // End call breeze

        // Clean unnecessary files from breeze
        $this->components->task('Cleaning unnecessary files from breeze...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
            (new Filesystem)->deleteDirectory(resource_path('js/Components'));
            (new Filesystem)->deleteDirectory(resource_path('js/Layouts'));
        });
        $this->info('Unnecessary files cleaned.');
        // End clean unnecessary files from breeze

        // Update the "package.json" file
        $this->components->task('Updating the "package.json" file...', function () {
            $this->updateNodePackages(function ($packages) {
                return [
                    // example
                    // 'axios' => '^0.21',
                    // ...
                    ] + $packages;
            });
        });
        $this->info('The "package.json" file updated.');
        // End update the "package.json" file

        // Controllers
        $this->components->task('Creating controllers...', function () {
            // (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
            // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/react/app/Http/Controllers', app_path('Http/Controllers'));
        });
        $this->info('Controllers created.');
        // End controllers

        // Views/Pages
        $this->components->task('Creating views/pages...', function () {
            // (new Filesystem)->ensureDirectoryExists(resource_path('views'));
            // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/react/resources/views', resource_path('views'));
        });
        $this->info('Views/pages created.');
        // End views/Pages

        // Routes
        $this->components->task('Creating routes...', function () {
            // copy(__DIR__.'/../../stubs/react/routes/web.php', base_path('routes/web.php'));
        });
        $this->info('Routes created.');
        // End routes

        // Update npm packages
        $this->components->task('Installing new npm module and build...', function () {
            $this->runCommands(['npm install', 'npm run build']);
        });
        $this->info('Npm module installed.');
        // End update npm packages

        // Copy default
        $this->components->task('Copying default template...', function () {
            $this->copyDefault();
        });
        $this->info('Default template copied.');
        // End copy default

        $this->line('');
        $this->components->info('PKT Starter Kit installed with React stack.');
    }
}