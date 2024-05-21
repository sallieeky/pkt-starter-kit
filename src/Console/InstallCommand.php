<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
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
            // $this->installReactStack();
            $this->info('React stack is not available yet. Please use "vue" stack.');
            return 1;
        } else {
            $this->error('Invalid stack. Please use "vue", or "react".');
            return 1;
        }
        return 1;
    }

    /**
     * Installing Laravel ComposerPackage if not exist
     *
     * @return void
     */
    protected function installComposerPackageIfNotExist()
    {
        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['laravel/breeze']);
        if (!$existInComposer) {
            $this->runCommands(['composer require laravel/breeze:v1.29 --dev']);
        }

        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['spatie/laravel-permission']);
        if (!$existInComposer) {
            $this->runCommands(['composer require spatie/laravel-permission:^6.1']);
        }

        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['doctrine/dbal']);
        if (!$existInComposer) {
            $this->runCommands(['composer require doctrine/dbal:*']);
        }

        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['pusher/pusher-php-server']);
        if (!$existInComposer) {
            $this->runCommands(['composer require pusher/pusher-php-server:^7.2']);
        }
        
        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['laravel/reverb']);
        if (!$existInComposer) {
            $this->runCommands(['composer require laravel/reverb:@beta']);
        }

        $existInComposer = file_exists(base_path('composer.json')) &&
            ! empty(json_decode(file_get_contents(base_path('composer.json')), true)['require']['staudenmeir/laravel-migration-views']);
        if (!$existInComposer) {
            $this->runCommands(['composer require staudenmeir/laravel-migration-views:^1.7']);
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
            // End Docker

            // Tailwind
            copy(__DIR__.'/../../stubs/default/tailwind.config.js', base_path('tailwind.config.js'));
            // End Tailwind

            // Exceptions
            (new Filesystem)->ensureDirectoryExists(app_path('Exceptions'));
            copy(__DIR__.'/../../stubs/default/app/Exceptions/Handler.php', app_path('Exceptions/Handler.php'));
            // End Exceptions

            // Controllers
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Api'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/AuthenticationController.php', app_path('Http/Controllers/AuthenticationController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/AccountController.php', app_path('Http/Controllers/AccountController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/RoleAndPermissionController.php', app_path('Http/Controllers/RoleAndPermissionController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/UserController.php', app_path('Http/Controllers/UserController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/UserLogController.php', app_path('Http/Controllers/UserLogController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/NotificationController.php', app_path('Http/Controllers/NotificationController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/GlobalSearchController.php', app_path('Http/Controllers/GlobalSearchController.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Controllers/Api/SsoSessionController.php', app_path('Http/Controllers/Api/SsoSessionController.php'));

            // End Controllers

            // Helpers
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Helpers'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Helpers', app_path('Http/Helpers'));
            // End Helpers

            // Middleware
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Middleware'));
            copy(__DIR__.'/../../stubs/default/app/Http/Middleware/HandleInertiaRequests.php', app_path('Http/Middleware/HandleInertiaRequests.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Middleware/UserActivityLog.php', app_path('Http/Middleware/UserActivityLog.php'));
            copy(__DIR__.'/../../stubs/default/app/Http/Middleware/SsoPortal.php', app_path('Http/Middleware/SsoPortal.php'));
            $this->installMiddlewareAfter('\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class', '\App\Http\Middleware\UserActivityLog::class');
            $this->installMiddlewareAliases("'SsoPortal' => \App\Http\Middleware\SsoPortal::class,");
            // End Middleware

            // Requests
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', app_path('Http/Requests'));
            // End Requests

            // Models
            (new Filesystem)->ensureDirectoryExists(app_path('Models'));
            copy(__DIR__.'/../../stubs/default/app/Models/User.php', app_path('Models/User.php'));
            copy(__DIR__.'/../../stubs/default/app/Models/SSOSession.php', app_path('Models/SSOSession.php'));
            // End Models

            // Providers
            (new Filesystem)->ensureDirectoryExists(app_path('Providers'));
            copy(__DIR__.'/../../stubs/default/app/Providers/RouteServiceProvider.php', app_path('Providers/RouteServiceProvider.php'));
            // End Providers

            // Config
            copy(__DIR__.'/../../stubs/default/config/ldap.php', config_path('ldap.php'));
            copy(__DIR__.'/../../stubs/default/config/sso-session.php', config_path('sso-session.php'));
            copy(__DIR__.'/../../stubs/default/config/logging.php', config_path('logging.php'));
            copy(__DIR__.'/../../stubs/default/config/permissions.php', config_path('permissions.php'));
            copy(__DIR__.'/../../stubs/default/config/leader.php', config_path('leader.php'));
            copy(__DIR__.'/../../stubs/default/config/reverb.php', config_path('reverb.php'));
            // End Config

            // Factories
            (new Filesystem)->ensureDirectoryExists(database_path('factories'));
            copy(__DIR__.'/../../stubs/default/database/factories/UserFactory.php', database_path('factories/UserFactory.php'));
            // End Factories

            // Migrations
            (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
            copy(__DIR__.'/../../stubs/default/database/migrations/2014_10_12_000000_create_users_table.php', database_path('migrations/2014_10_12_000000_create_users_table.php'));
            copy(__DIR__.'/../../stubs/default/database/migrations/2024_04_01_000000_create_permission_tables.php', database_path('migrations/2024_04_01_000000_create_permission_tables.php'));
            copy(__DIR__.'/../../stubs/default/database/migrations/2024_04_01_000000_create_sso_sessions_table.php', database_path('migrations/2024_04_01_000000_create_sso_sessions_table.php'));
            copy(__DIR__.'/../../stubs/default/database/migrations/2024_05_01_000000_create_notifications_table.php', database_path('migrations/2024_05_01_000000_create_notifications_table.php'));
            
            // End Migrations

            // Seeders
            (new Filesystem)->ensureDirectoryExists(database_path('seeders'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/database/seeders', database_path('seeders'));
            // End Seeders

            // Public
            (new Filesystem)->ensureDirectoryExists(public_path('images'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/public/images', public_path('images'));
            (new Filesystem)->ensureDirectoryExists(public_path('icons'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/public/icons', public_path('icons'));
            // End Public

            // Resources
            (new Filesystem)->ensureDirectoryExists(resource_path('css'));
            copy(__DIR__.'/../../stubs/default/resources/css/dx.material.pkt-scheme.css', resource_path('css/dx.material.pkt-scheme.css'));
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Core'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/resources/js/Core', resource_path('js/Core'));
            (new Filesystem)->ensureDirectoryExists(resource_path('views/errors'));
            copy(__DIR__.'/../../stubs/default/resources/views/errors/403.blade.php', resource_path('views/errors/403.blade.php'));
            copy(__DIR__.'/../../stubs/default/resources/views/errors/404.blade.php', resource_path('views/errors/404.blade.php'));
            copy(__DIR__.'/../../stubs/default/resources/views/errors/500.blade.php', resource_path('views/errors/500.blade.php'));
            // End Resources

            // Routes
            (new Filesystem)->ensureDirectoryExists(base_path('routes'));
            copy(__DIR__.'/../../stubs/default/routes/web.php', base_path('routes/web.php'));
            copy(__DIR__.'/../../stubs/default/routes/api.php', base_path('routes/api.php'));
            copy(__DIR__.'/../../stubs/default/routes/channels.php', base_path('routes/channels.php'));
            copy(__DIR__.'/../../stubs/default/routes/starter.php', base_path('routes/starter.php'));
            (new Filesystem)->delete(base_path('routes/auth.php'));
            // End Routes

            // Test
            (new Filesystem)->deleteDirectory(base_path('tests'));
            (new Filesystem)->ensureDirectoryExists(base_path('tests'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/tests', base_path('tests'));
            copy(__DIR__.'/../../stubs/default/phpunit.xml', base_path('phpunit.xml'));
            // End Test

            // SSL
            (new Filesystem)->ensureDirectoryExists(base_path('ssl'));
            (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/ssl', base_path('ssl'));
            // End SSL

            // Reverb
            $this->addEnvironmentVariables();
            $this->updateBroadcastingConfiguration();
            $this->enableBroadcasting();
            // End Reverb
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

    /**
     * Install the middleware to a aliases in the application Http Kernel.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     * @return void
     */
    protected function installMiddlewareAliases($name)
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareAliases = Str::before(Str::after($httpKernel, '$middlewareAliases = ['), '];');
        $middlewareAlias = Str::before(Str::after($middlewareAliases, "'auth' => \App\Http\Middleware\Authenticate::class,"), ',');

        $modifiedMiddlewareAlias = str_replace(
            "'auth' => \App\Http\Middleware\Authenticate::class,",
            "'auth' => \App\Http\Middleware\Authenticate::class,".PHP_EOL.'        '.$name,
            $middlewareAliases,
        );

        file_put_contents(app_path('Http/Kernel.php'), str_replace(
            $middlewareAliases,
            $modifiedMiddlewareAlias,
            $httpKernel
        ));
    }

    /**
     * Add the Reverb variables to the environment file.
     */
    protected function addEnvironmentVariables(): void
    {
        if (File::missing($env = app()->environmentFile())) {
            return;
        }

        $contents = File::get($env);
        $appId = random_int(100_000, 999_999);
        $appKey = Str::lower(Str::random(20));
        $appSecret = Str::lower(Str::random(20));

        $variables = Arr::where([
            'REVERB_APP_ID' => "REVERB_APP_ID={$appId}",
            'REVERB_APP_KEY' => "REVERB_APP_KEY={$appKey}",
            'REVERB_APP_SECRET' => "REVERB_APP_SECRET={$appSecret}",
            'REVERB_HOST' => 'REVERB_HOST="localhost"',
            'REVERB_PORT' => 'REVERB_PORT=8080',
            'REVERB_SCHEME' => 'REVERB_SCHEME=http',
            'REVERB_NEW_LINE' => null,
            'VITE_REVERB_APP_KEY' => 'VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"',
            'VITE_REVERB_HOST' => 'VITE_REVERB_HOST="${REVERB_HOST}"',
            'VITE_REVERB_PORT' => 'VITE_REVERB_PORT="${REVERB_PORT}"',
            'VITE_REVERB_SCHEME' => 'VITE_REVERB_SCHEME="${REVERB_SCHEME}"',
        ], function ($value, $key) use ($contents) {
            return ! Str::contains($contents, PHP_EOL.$key);
        });

        $variables = trim(implode(PHP_EOL, $variables));

        if ($variables === '') {
            return;
        }

        File::append(
            $env,
            Str::endsWith($contents, PHP_EOL) ? PHP_EOL.$variables.PHP_EOL : PHP_EOL.PHP_EOL.$variables.PHP_EOL,
        );
    }

    /**
     * Update the broadcasting.php configuration file.
     */
    protected function updateBroadcastingConfiguration(): void
    {
        if ($this->laravel->config->has('broadcasting.connections.reverb')) {
            return;
        }

        File::replaceInFile(
            "'connections' => [\n",
            <<<'CONFIG'
            'connections' => [

                    'reverb' => [
                        'driver' => 'reverb',
                        'key' => env('REVERB_APP_KEY'),
                        'secret' => env('REVERB_APP_SECRET'),
                        'app_id' => env('REVERB_APP_ID'),
                        'options' => [
                            'host' => env('REVERB_HOST'),
                            'port' => env('REVERB_PORT', 443),
                            'scheme' => env('REVERB_SCHEME', 'https'),
                            'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
                        ],
                        'client_options' => [
                            // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
                        ],
                    ],

            CONFIG,
            app()->configPath('broadcasting.php')
        );
    }

    /**
     * Enable Laravel's broadcasting functionality.
     */
    protected function enableBroadcasting(): void
    {
        $this->enableBroadcastServiceProvider();

        if (File::exists(base_path('routes/channels.php'))) {
            return;
        }

        $enable = confirm('Would you like to enable event broadcasting?', default: true);

        if (! $enable) {
            return;
        }

        if ($this->getApplication()->has('install:broadcasting')) {
            $this->call('install:broadcasting', ['--no-interaction' => true]);
        }
    }

    /**
     * Uncomment the "BroadcastServiceProvider" in the application configuration.
     */
    protected function enableBroadcastServiceProvider(): void
    {
        $config = File::get(app()->configPath('app.php'));

        if (Str::contains($config, '// App\Providers\BroadcastServiceProvider::class')) {
            File::replaceInFile(
                '// App\Providers\BroadcastServiceProvider::class',
                'App\Providers\BroadcastServiceProvider::class',
                app()->configPath('app.php'),
            );
        }
    }
}
