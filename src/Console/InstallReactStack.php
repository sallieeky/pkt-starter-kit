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
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
        (new Filesystem)->deleteDirectory(resource_path('js/Components'));
        (new Filesystem)->deleteDirectory(resource_path('js/Layouts'));
        // End clean unnecessary files from breeze

        // Update the "package.json" file
        $this->updateNodePackages(function ($packages) {
            return [
                // example
                // 'axios' => '^0.21',
                // ...
                ] + $packages;
        });
        // End update the "package.json" file

        // Controllers
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/app/Http/Controllers', app_path('Http/Controllers'));
        // End controllers

        // Views/Pages
        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/views', resource_path('views'));
        // End views/Pages

        // Routes
        copy(__DIR__.'/../../stubs/vue/routes/web.php', base_path('routes/web.php'));
        // End routes

        // Update npm packages
        $this->runCommands(['npm install', 'npm run build']);
        // End update npm packages

        $this->line('');

        $this->components->info('PKT Starter Kit installed with React stack.');
    }
}