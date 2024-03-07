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
        $this->runCommands(['php artisan breeze:install vue']);
        // End call breeze

        // Clean unnecessary files from breeze
        $this->components->info('Cleaning unnecessary files from breeze...');
        // ...
        $this->components->info('Unnecessary files cleaned.');
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
        $this->components->info('The "package.json" file updated.');
        // End update the "package.json" file

        // Controllers
        $this->components->info('Creating controllers...');
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/app/Http/Controllers', app_path('Http/Controllers'));
        $this->components->info('Controllers created.');
        // End controllers

        // Views/Pages
        $this->components->info('Creating views/pages...');
        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/views', resource_path('views'));
        $this->components->info('Views/pages created.');
        // End views/Pages

        // Routes
        $this->components->info('Creating routes...');
        copy(__DIR__.'/../../stubs/vue/routes/web.php', base_path('routes/web.php'));
        $this->components->info('Routes created.');
        // End routes

        // Update npm packages
        $this->components->info('Installing new npm module and build...');
        $this->runCommands(['npm install', 'npm run build']);
        $this->components->info('Npm module installed.');
        // End update npm packages

        // Copy default
        $this->components->info('Copying default template...');
        $this->copyDefault();
        $this->components->info('Default template copied.');
        // End copy default

        $this->line('');
        $this->components->info('PKT Starter Kit installed with Vue stack.');
    }
}