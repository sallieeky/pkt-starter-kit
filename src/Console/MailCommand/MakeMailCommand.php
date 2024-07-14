<?php

namespace Pkt\StarterKit\Console\MailCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeMailCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-mail 
                    {name : mail name e.g. PendingOrder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create mail file and view';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $view = Str::snake($name, '-');

        (new Filesystem)->ensureDirectoryExists(resource_path('views/mails'));
        (new Filesystem)->ensureDirectoryExists(app_path('Mail'));
        copy(__DIR__ . '/../../../mail-stubs/app/Mail/Mail.php', app_path("Mail/{$name}.php"));
        copy(__DIR__ . '/../../../mail-stubs/resources/views/mails/mail.blade.php', resource_path("views/mails/{$view}.blade.php"));

        $subject = Str::title($name);
        $this->replaceContent(app_path("Mail/{$name}.php"), [
            'ClassName' => $name,
            '\'Subject\'' => "'{$subject}'",
            '\'view-name\'' => "'mails.{$view}'",
        ]);

        $this->components->info("Mail file app/Mail/{$name}.php created successfully.");
        $this->components->info("View file resource/views/mails/{$view}.blade.php created successfully.");
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
