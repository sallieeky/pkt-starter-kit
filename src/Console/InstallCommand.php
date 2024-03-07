<?php

namespace Pkt\StarterKit\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Process\Process;


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
            $this->runCommands(['composer require laravel/breeze --dev']);
        }
    }

    /**
     * Copying default template
     *
     * @return void
     */
    protected function copyDefault()
    {
        // Controllers
        // (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Controllers', app_path('Http/Controllers'));
        // End controllers
        
        // ...
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
}
