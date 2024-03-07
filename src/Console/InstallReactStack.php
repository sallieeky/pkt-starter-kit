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
        $this->components->info('Cleaning unnecessary files from breeze...');
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
        (new Filesystem)->deleteDirectory(resource_path('js/Components'));
        (new Filesystem)->deleteDirectory(resource_path('js/Layouts'));
        $this->components->success('Unnecessary files cleaned.');
        // End clean unnecessary files from breeze

        // Update the "package.json" file
        $this->components->info('Updating the "package.json" file...');
        $this->updateNodePackages(function ($packages) {
            return [
                // example
                // 'axios' => '^0.21',
                // ...
                ] + $packages;
        });
        $this->components->success('The "package.json" file updated.');
        // End update the "package.json" file

        // Controllers
        $this->components->info('Creating controllers...');
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/react/app/Http/Controllers', app_path('Http/Controllers'));
        $this->components->success('Controllers created.');
        // End controllers

        // Views/Pages
        $this->components->info('Creating views/pages...');
        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/react/resources/views', resource_path('views'));
        $this->components->success('Views/pages created.');
        // End views/Pages

        // Routes
        $this->components->info('Creating routes...');
        copy(__DIR__.'/../../stubs/react/routes/web.php', base_path('routes/web.php'));
        $this->components->success('Routes created.');
        // End routes

        // Update npm packages
        $this->components->info('Installing new npm module and build...');
        $this->runCommands(['npm install', 'npm run build']);
        $this->components->success('Npm module installed.');
        // End update npm packages

        $this->line('');
        $this->components->info('PKT Starter Kit installed with React stack.');
    }
}