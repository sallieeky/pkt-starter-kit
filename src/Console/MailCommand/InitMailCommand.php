<?php

namespace Pkt\StarterKit\Console\MailCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;

class InitMailCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:mail-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize mail stubs to your project';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if (file_exists(resource_path('views/components/mails/layouts/app.blade.php'))) {
            $this->components->error('You already have initialize mail.');
            return 1;
        }

        (new Filesystem)->ensureDirectoryExists(resource_path('views/components/mails'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../../mail-stubs/resources/views/components/mails', resource_path('views/components/mails'));

        $this->components->info('Mail initialized successfully.');
        return 1;
    }

    /**
     * Replace content in file
     *
     * @param string $file
     * @param array $replacements
     * @return void
     */
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
    }
}
