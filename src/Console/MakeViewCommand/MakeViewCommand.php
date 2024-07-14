<?php

namespace Pkt\StarterKit\Console\MakeViewCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeViewCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-view
        {name : The name of the view model}
        {--raw : Create view table with raw query}
        {--model= : The name of the related model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database view table';

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
        $migrationName = date('Y_m_d_His') . '_create_' . $viewName . '_view.php';
        $migrationPath = database_path('migrations/' . $migrationName);
        
        if (file_exists($path) || file_exists($migrationPath)) {
            $this->error('View already exists!');
            return 0;
        }

        if ($this->option('model') && !class_exists('App\Models\\'.$this->option('model'))) {
            $this->error('Model not found!');
            return 0;
        }

        $type = $this->option('raw') ? 'raw' : $this->choice('Select the creation type of view table', ['eloquent', 'raw'], 'eloquent');

        if ($type === 'eloquent') {
            if (!$this->option('model')) {
                $modelFiles = File::allFiles(app_path('Models'));
                $modelFiles = array_filter($modelFiles, function($file) {
                    return !str_contains($file->getRelativePathname(), 'Views/');
                });
                $model = [];
                foreach ($modelFiles as $file) {
                    $modelPathname = $file->getRelativePathname();
                    $modelPathname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $modelPathname);
                    $modelPathname = str_replace('/', '\\', $modelPathname);
                    $model[] = $modelPathname;
                }
                $relatedModel = $this->choice('Select the related model', $model);
            } else {
                $relatedModel = $this->option('model');
            }

            copy(__DIR__.'/../../../database-view-stubs/migrations/create_view_table.php', $migrationPath);
            $this->replaceContent($migrationPath, [
                'ModelName' => $relatedModel,
                'table_name' => $viewName,
            ]);
        } else if ($type === 'raw') {
            if (!$this->option('model')) {
                $existingTables = collect(DB::connection()->getDoctrineSchemaManager()->listTableNames())->filter(function ($table) {
                    return !in_array($table, [
                        'failed_jobs',
                        'mediables',
                        'migrations',
                        'model_has_permissions',
                        'model_has_roles',
                        'notifications',
                        'password_reset_tokens',
                        'permissions',
                        'personal_access_tokens',
                        'role_has_permissions',
                        'roles',
                        'sso_sessions'
                    ]);
                })->values();
                $relatedTable = $this->choice('Select related table name', $existingTables->toArray(), 0);
            } else {
                $model = app('App\Models\\'.$this->option('model'));
                $relatedTable = $model->getTable();
            }
            
            copy(__DIR__.'/../../../database-view-stubs/migrations/create_query_view_table.php', $migrationPath);
            $this->replaceContent($migrationPath, [
                'table_name' => $viewName,
                'related_table' => $relatedTable,
            ]);
        }

        (new Filesystem)->ensureDirectoryExists(app_path('Models/Views'));
        copy(__DIR__.'/../../../database-view-stubs/Models/ModelView.php', $path);
        $this->replaceContent($path, [
            'ModelName' => $modelName,
            'table_name' => $viewName,
        ]);

        $this->components->info('Model file created: '.'app/Models/Views/'.$modelName.'.php');
        $this->components->info('Migration file created: '.'database/migrations/'.$migrationName);
        
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
