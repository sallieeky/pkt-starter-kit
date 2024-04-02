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
        $this->installComposerPackageIfNotExist();
        $this->runCommands(['php artisan breeze:install vue --pest']);
        // End call breeze

        // Clean unnecessary files from breeze
        $this->components->task('Cleaning unnecessary files from breeze...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages'));
            (new Filesystem)->deleteDirectory(resource_path('js/Components'));
            (new Filesystem)->deleteDirectory(resource_path('js/Layouts'));
            (new Filesystem)->deleteDirectory(resource_path('js/Pages'));

            (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
            (new Filesystem)->deleteDirectory(app_path('Http/Controllers/Auth'));
            (new Filesystem)->delete(app_path('Http/Controllers/ProfileController.php'));
            
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
            (new Filesystem)->deleteDirectory(app_path('Http/Requests'));
        });
        // End clean unnecessary files from breeze

        // Copy default
        $this->components->task('Copying default template...', function () {
            $this->copyDefault();
        });
        // End copy default

        // Update the "package.json" file
        $this->components->task('Updating the "package.json" file...', function () {
            copy(__DIR__.'/../../stubs/vue/package.json', base_path('package.json'));
        });
        // End update the "package.json" file

        // Vite
        $this->components->task('Creating vite config...', function () {
            copy(__DIR__.'/../../stubs/vue/vite.config.js', base_path('vite.config.js'));
        });
        // End vite

        // Css
        $this->components->task('Creating css...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('css'));
            copy(__DIR__.'/../../stubs/vue/resources/css/app.css', resource_path('css/app.css'));
            copy(__DIR__.'/../../stubs/vue/resources/css/element-plus.scss', resource_path('css/element-plus.scss'));
        });
        // End Css

        // Views/Pages
        $this->components->task('Creating views/pages...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('views'));
            copy(__DIR__.'/../../stubs/vue/resources/views/app.blade.php', resource_path('views/app.blade.php'));
        });
        // End views/Pages

        // Js
        $this->components->task('Creating js...', function () {
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Layouts'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Stores'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/js/Components', resource_path('js/Components'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/js/Layouts', resource_path('js/Layouts'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/js/Pages', resource_path('js/Pages'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/vue/resources/js/Stores', resource_path('js/Stores'));
            copy(__DIR__.'/../../stubs/vue/resources/js/app.js', resource_path('js/app.js'));
            copy(__DIR__.'/../../stubs/vue/resources/js/bootstrap.js', resource_path('js/bootstrap.js'));
        });
        // End Js

        // Update npm packages
        $this->components->task('Installing new npm module and build...', function () {
            (new Filesystem)->delete(base_path('package-lock.json'));
            $this->runCommands(['npm install', 'npm run build']);
        });
        // End update npm packages

        $this->line('');
        $this->components->info('PKT Starter Kit installed with Vue stack.');
    }
}