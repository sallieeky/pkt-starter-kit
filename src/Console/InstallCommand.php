<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InstallCommand extends Command implements PromptsForMissingInput
{
    use InstallVueStack, InstallReactStack;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:install 
                            {stack : The development stack that should be installed (vue or react)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Pkt Starter Kit into the application.';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if (!$this->confirm('Make sure that this is the empty project. Do you want to continue?')) {
            $this->info('Installation aborted.');
            return 1;
        }
        
        if ($this->argument('stack') === 'vue') {
            $this->installVueStack();
        } elseif ($this->argument('stack') === 'react') {
            $this->installReactStack();
        } else {
            $this->error('Invalid stack. Please use "vue", or "react".');
            return 1;
        }
        return 1;
    }

    /**
     * Installing Laravel Breeze if not exist
     *
     * @return void
     */
    protected function installBreezeIfNotExist()
    {
        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['laravel/breeze']);
        if (!$existInComposer) {
            $this->runCommands(['composer require laravel/breeze:v1.29.1 --dev']);
        }
    }

    /**
     * Copying default template
     *
     * @return void
     */
    protected function copyDefault()
    {

        $this->components->task('Copying default...', function () {
            // Docker
            (new Filesystem)->ensureDirectoryExists(base_path('.docker'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/.docker', base_path('.docker'));
            copy(__DIR__.'/../../stubs/default/docker-compose.yml', base_path('docker-compose.yml'));
            // End docker

            // Tailwind
            copy(__DIR__.'/../../stubs/default/tailwind.config.js', base_path('tailwind.config.js'));
            // End tailwind

            // Controllers
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/AuthenticationController.php', app_path('Http/Controllers/AuthenticationController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/RoleAndPermissionController.php', app_path('Http/Controllers/RoleAndPermissionController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/UserController.php', app_path('Http/Controllers/UserController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/UserLogController.php', app_path('Http/Controllers/UserLogController.php'));
            // End controllers

            // Helpers
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Helpers'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Helpers', app_path('Http/Helpers'));
            // End Helpers

            // Middleware
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Middleware'));
            copy(__DIR__.'/../../stubs/default/app/Http/Middleware/UserActivityLog.php', app_path('Http/Middleware/UserActivityLog.php'));
            $this->installMiddlewareAfter('\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class', '\App\Http\Middleware\UserActivityLog::class');
            // End Middleware

            // Requests
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', app_path('Http/Requests'));
            // End Requests

            // Public
            (new Filesystem)->ensureDirectoryExists(public_path('images'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/public/images', public_path('images'));
            (new Filesystem)->ensureDirectoryExists(public_path('icons'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/public/icons', public_path('icons'));
            // End Public

            // Resources
            copy(__DIR__.'/../../stubs/default/resources/css/dx.material.pkt-scheme.css', resource_path('css/dx.material.pkt-scheme.css'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Core'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/resources/js/Core', resource_path('js/Core'));
            // End Resources
        });
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (\RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  array  $packages
     * @param  bool  $asDev
     * @return bool
     */
    protected function requireComposerPackages(array $packages, $asDev = false)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (! Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after.',',
                $after.','.PHP_EOL.'            '.$name.',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }
}
