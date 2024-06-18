<?php

namespace Pkt\StarterKit\Console\MakeDatabaseTableCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeDatabaseTableCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-table 
                    {name : table name (plural) e.g. users / model name (singular) e.g. User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create table migration file and model';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $name = $this->argument('name');
        $name = Str::singular(ucfirst($name));
        $singularName = Str::singular($name);
        $pluralName = Str::plural($name);

        // check if model already exists
        if (file_exists(app_path('Models/'.Str::studly($singularName).'.php'))) {
            $this->error('Model already exists: '.app_path('Models/'.Str::studly($singularName).'.php'));
            return 0;
        }

        // ask select master, transaction, or value list
        $type = $this->choice('Select table type', ['transaction (tr)','master (ms)','value list (vl)'], 0);
        $type = match ($type) {
            'transaction (tr)' => 'tr',
            'master (ms)' => 'ms',
            'value list (vl)' => 'vl',
        };
        $tableName = Str::snake($type.$pluralName);
        $columnName = Str::snake($type.$singularName);

        // Create migration file
        $migrationName = date('Y_m_d_His').'_create_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-table-stubs/migrations/migration.php', database_path('migrations/'.$migrationName));
        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'TableNames' => $tableName,
            'table_name_id' => $columnName.'_id',
            'table_name_uuid' => $columnName.'_uuid',
        ]);

        // Create factory file
        $factoryName = Str::studly($singularName).'Factory';
        $factoryPath = database_path('factories/'.$factoryName.'.php');
        copy(__DIR__.'/../../../database-table-stubs/factories/factory.php', $factoryPath);
        $this->replaceContent($factoryPath, [
            'ModelName' => Str::studly($singularName),
        ]);

        // Create model file
        $modelName = Str::studly($singularName);
        $modelPath = app_path('Models/'.$modelName.'.php');
        copy(__DIR__.'/../../../database-table-stubs/Models/model.php', $modelPath);
        $this->replaceContent($modelPath, [
            'ModelName' => $modelName,
            'table_names' => $tableName,
            'table_name_id' => $columnName.'_id',
            'table_name_uuid' => $columnName.'_uuid',
        ]);

        $this->info('Model file created: '.'app/Models/'.$modelName.'.php');
        $this->info('Migration file created: '.'database/migrations/'.$migrationName);
        $this->info('Factory file created: '.'database/factories/'.$factoryName.'.php');

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
