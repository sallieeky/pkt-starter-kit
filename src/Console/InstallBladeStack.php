<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallBladeStack
{
    protected function installBladeStack()
    {
        $this->components->info('Installing Blade stack...');

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    'autoprefixer' => '^10.4.12',
                    'postcss' => '^8.4.31',
                    'tailwindcss' => '^3.2.1',
                    'alpinejs' => '^3.13.5',
                ] + $packages;
        });

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/blade/app/Http/Controllers', app_path('Http/Controllers'));

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/blade/resources/views', resource_path('views'));

        // Routes...
        copy(__DIR__.'/../../stubs/blade/routes/web.php', base_path('routes/web.php'));

        // Tailwind / Vite...
        copy(__DIR__.'/../../stubs/blade/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/blade/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../stubs/blade/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/blade/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__.'/../../stubs/blade/resources/js/app.js', resource_path('js/app.js'));

        $this->components->info('Installing and building Node dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run build']);
        } else {
            $this->runCommands(['npm install', 'npm run build']);
        }

        $this->line('');
        $this->components->info('Eky scaffolding installed successfully.');
    }
}