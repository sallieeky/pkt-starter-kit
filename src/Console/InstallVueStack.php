<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallVueStack
{
    protected function installVueStack()
    {
        $this->components->info('Installing Vue stack...');
        $this->line('');

        // Call breeze
        $this->installBreezeIfNotExist();
        $this->runCommands(['php artisan breeze:install vue --pest']);
        // End call breeze

        // Clean unnecessary files from breeze
        $this->components->task('Cleaning unnecessary files from breeze...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
            (new Filesystem)->deleteDirectory(resource_path('js/Components'));
            (new Filesystem)->deleteDirectory(resource_path('js/Layouts'));
        });
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
        // End update the "package.json" file

        // Controllers
        $this->components->task('Creating controllers...', function () {
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/app/Http/Controllers', app_path('Http/Controllers'));
        });
        // End controllers

        // Views/Pages
        $this->components->task('Creating views/pages...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('views'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/views', resource_path('views'));
        });
        // End views/Pages

        // Routes
        $this->components->task('Creating routes...', function () {
            // copy(__DIR__.'/../../stubs/vue/routes/web.php', base_path('routes/web.php'));
        });
        // End routes

        // Update npm packages
        $this->components->task('Installing new npm module and build...', function () {
            $this->runCommands(['npm install', 'npm run build']);
        });
        // End update npm packages

        // Copy default
        $this->components->task('Copying default template...', function () {
            $this->copyDefault();
        });
        // End copy default

        $this->line('');
        $this->components->info('PKT Starter Kit installed with Vue stack.');
    }
}