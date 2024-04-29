<?php

namespace Pkt\StarterKit\Console\MakeViewCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeViewCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-view
        {name : The name of the view model}
        {--model= : The name of the related model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make database view table';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $modelName = $this->argument('name');
        $path = app_path('Models/Views/' . $modelName . '.php');
        $viewName = Str::snake(Str::pluralStudly($modelName));
        $migrationPath = database_path('migrations/' . date('Y_m_d_His') . '_create_' . $viewName . '_view.php');
        
        if (file_exists($path) || file_exists($migrationPath)) {
            $this->error('View already exists!');
            return 0;
        }
        
        (new Filesystem)->ensureDirectoryExists(app_path('Models/Views'));
        copy(__DIR__.'/../../../database-view-stubs/Models/ModelView.php', $path);
        $this->replaceContent($path, [
            'ModelName' => $modelName,
            'table_name' => $viewName,
        ]);

        $relatedModel = $this->option('model') ?? $modelName;
        copy(__DIR__.'/../../../database-view-stubs/migrations/create_view_table.php', $migrationPath);
        $this->replaceContent($migrationPath, [
            'ModelName' => $relatedModel,
            'table_name' => $viewName,
        ]);
        
        $this->info('View created successfully.');
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
