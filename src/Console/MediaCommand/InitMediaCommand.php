<?php

namespace Pkt\StarterKit\Console\MediaCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class InitMediaCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:media-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize application with media';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $this->info('Initializing media...');

        // check if media already initialized
        if (file_exists(app_path('Http/Controllers/MediaController.php')) || file_exists(app_path('Models/Media.php')) || file_exists(database_path('migrations/2024_06_11_000000_create_media_table.php')) || file_exists(database_path('migrations/2024_06_11_00000_create_mediables_table.php'))){
            $this->error('Media already initialized.');
            return 0;
        }

        // copy media controller
        $this->components->task('Copying media controller...', function () {
            if (!file_exists(app_path('Http/Controllers/MediaController.php'))) {
                copy(__DIR__.'/../../../media-stubs/app/Http/Controllers/MediaController.php', app_path('Http/Controllers/MediaController.php'));
            }
        });

        // copy media model
        $this->components->task('Copying media model...', function () {
            if (!file_exists(app_path('Models/Media.php'))) {
                copy(__DIR__.'/../../../media-stubs/app/Models/Media.php', app_path('Models/Media.php'));
            }
        });

        // copy media migration
        $this->components->task('Copying media migrations...', function () {
            if (!file_exists(database_path('migrations/2024_06_11_000000_create_media_table.php'))) {
                copy(__DIR__.'/../../../media-stubs/database/migrations/2024_06_11_000000_create_media_table.php', database_path('migrations/2024_06_11_000000_create_media_table.php'));
            }
            if (!file_exists(database_path('migrations/2024_06_11_000000_create_mediables_table.php'))) {
                copy(__DIR__.'/../../../media-stubs/database/migrations/2024_06_11_000000_create_mediables_table.php', database_path('migrations/2024_06_11_00000_create_mediables_table.php'));
            }
        });

        $this->info('Media initialized successfully.');
        $this->line('');
        $this->info('Add this to your web.php or starter.php file:');
        $this->info("Route::get('get-media/{media:uuid}', App\Http\Controllers\MediaController::class)->name('get-media');");
    }

    
    protected function replaceContent($file, $replacements)
    {
        $content = file_get_contents($file);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($file, $content);
     }
}
