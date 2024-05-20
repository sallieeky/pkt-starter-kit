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
                    {name : table name (singular) e.g. user}';

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
        $singularName = Str::singular($name);
        $pluralName = Str::plural($name);

        // ask select master, transaction, or value list
        $type = $this->choice('Select table type', ['transaction (tr)','master (ms)','value list (vl)'], 1);
        $type = match ($type) {
            'transaction (tr)' => 'tr',
            'master (ms)' => 'ms',
            'value list (vl)' => 'vl',
        };
        $tableName = Str::snake($type.$pluralName);
        $columnName = Str::snake($singularName);

        // Create migration file
        $migrationName = date('Y_m_d_His').'_create_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-table-stubs/migrations/migration.php', database_path('migrations/'.$migrationName));
        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'TableNames' => $tableName,
            'table_name_id' => $columnName.'_id',
            'table_name_uuid' => $columnName.'_uuid',
        ]);

        // Create model file
        $modelName = Str::studly($singularName);
        $modelPath = app_path('Models/'.$modelName.'.php');
        copy(__DIR__.'/../../../database-table-stubs/Models/model.php', $modelPath);
        $this->replaceContent($modelPath, [
            'ModelName' => $modelName,
            'table_name_id' => $columnName.'_id',
            'table_name_uuid' => $columnName.'_uuid',
        ]);


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
