<?php

namespace Pkt\StarterKit\Console\MakeModelFromExistingTableCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModelFromExistingTableCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-model 
                {--table= : The existing table name}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a model from an existing table in the database';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $type = $this->option('table') ? 'One Table' : $this->choice('Do you want to sync all table and model or just one table?', ['All Tables', 'One Table']);
        $tableStatus = [];
        if ($type === 'All Tables') {
            $existingTables = collect(DB::connection()->getDoctrineSchemaManager()->listTableNames())->filter(function ($table) {
                return !in_array($table, [
                    'failed_jobs',
                    'mediables',
                    'media',
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

            $modelFiles = File::allFiles(app_path('Models'));
            $modelFiles = array_filter($modelFiles, function($file) {
                return !str_contains($file->getRelativePathname(), 'Views/');
            });
            $models = [];
            foreach ($modelFiles as $file) {
                $modelPathname = $file->getRelativePathname();
                $modelPathname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $modelPathname);
                $modelPathname = str_replace('/', '\\', $modelPathname);
                $models[] = $modelPathname;
            }

            foreach ($existingTables as $table) {
                $tableName = Str::lower($table);
                $tableName = Str::replaceFirst('tr_', '', $tableName);
                $tableName = Str::replaceFirst('ms_', '', $tableName);
                $tableName = Str::replaceFirst('vl_', '', $tableName);

                $modelName = Str::studly(Str::singular($tableName));
                $primaryKey = optional(optional(DB::connection()->getDoctrineSchemaManager()->listTableIndexes($table))['primary']->getColumns())[0];

                $modelExists = false;
                foreach ($models as $model) {
                    $model = app('App\Models\\'.$model);
                    if ($model->getTable() === $table) {
                        $modelExists = true;
                        break;
                    }
                }

                if ($modelExists) {
                    $tableStatus[] = [
                        'table' => $table,
                        'status' => false,
                        'model' => null,
                    ];
                    continue;
                } else {
                    copy(__DIR__.'/../../../additional-stubs/default/app/Models/BlankModel.php', app_path('Models/'.$modelName.'.php'));
                    $this->replaceContent(app_path('Models/'.$modelName.'.php'), [
                        'ModelName' => $modelName,
                        'table_names' => $table,
                        'table_name_id' => $primaryKey,
                    ]);

                    $tableStatus[] = [
                        'table' => $table,
                        'status' => true,
                        'model' => 'app/Models/'.$modelName.'.php',
                    ];
                }
            }
        } else if ($type === 'One Table') {
            $existingTables = collect(DB::connection()->getDoctrineSchemaManager()->listTableNames())->filter(function ($table) {
                return !in_array($table, [
                    'failed_jobs',
                    'mediables',
                    'media',
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

            if (!in_array($this->option('table'), $existingTables->toArray())) {
                $this->components->error('Table not found');
                return 1;
            }

            $modelFiles = File::allFiles(app_path('Models'));
            $modelFiles = array_filter($modelFiles, function($file) {
                return !str_contains($file->getRelativePathname(), 'Views/');
            });
            $models = [];
            foreach ($modelFiles as $file) {
                $modelPathname = $file->getRelativePathname();
                $modelPathname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $modelPathname);
                $modelPathname = str_replace('/', '\\', $modelPathname);
                $models[] = $modelPathname;
            }

            foreach ($models as $model) {
                $model = app('App\Models\\'.$model);
                if ($model->getTable() === $this->option('table')) {
                    $tableStatus[] = [
                        'table' => $this->option('table'),
                        'status' => false,
                        'model' => null,
                    ];
                    continue;
                } else {
                    $table = Str::lower($this->option('table'));
                    $table = Str::replaceFirst('tr_', '', $table);
                    $table = Str::replaceFirst('ms_', '', $table);
                    $table = Str::replaceFirst('vl_', '', $table);

                    $modelName = Str::studly(Str::singular($table));
                    copy(__DIR__.'/../../../additional-stubs/default/app/Models/BlankModel.php', app_path('Models/'.$modelName.'.php'));
                    $this->replaceContent(app_path('Models/'.$modelName.'.php'), [
                        'ModelName' => $modelName,
                        'table_names' => $table,
                        'table_name_id' => $table.'_id',
                    ]);

                    $tableStatus[] = [
                        'table' => $this->option('table'),
                        'status' => true,
                        'model' => 'app/Models/'.$modelName.'.php',
                    ];
                }
            }
        }

        $this->components->table(['Table', 'Status', 'Model'], $tableStatus);
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
