<?php

namespace Pkt\StarterKit\Console\MakeMigrationCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Pkt\StarterKit\Utils\MigrationSchemaBuilder;

use function Laravel\Prompts\multiselect;

class MakeMigrationCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration file for specific case';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $type = $this->choice('Select migration type', [
            'add column',
            'drop column',
            'rename column',
            'change column data type',
            'manipulate multiple columns / custom schema',
        ], 0);

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
        $tableName = $this->choice('Select table name', $existingTables->toArray(), 0);

        if ($type === 'add column') {
            $this->addColumn($tableName);
        } elseif ($type === 'drop column') {
            $this->dropColumn($tableName);
        } elseif ($type === 'rename column') {
            $this->renameColumn($tableName);
        } elseif ($type === 'change column data type') {
            $this->changeColumnDataType($tableName);
        } else if ($type === 'manipulate multiple columns / custom schema') {
            $this->manipulateMultipleColumns($tableName);
        } else {
            $this->components->error('Invalid migration type');
        }

        return 0;
    }

    /**
     * Add column to table
     *
     * @param string $tableName
     * @return void
     */
    private function addColumn($tableName)
    {
        $newColumnName = $this->ask('Enter new column name');
        $newColumnName = Str::snake($newColumnName);

        $dataType = $this->choiceDataType();
        $options = $this->choiceAdditionalOptions();

        $migrationName = date('Y_m_d_His').'_add_'.$newColumnName.'_to_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-migration-stubs/migration.php', database_path('migrations/'.$migrationName));

        $upMigrationSchema = MigrationSchemaBuilder::addColumn($newColumnName, $dataType, $options);
        $downMigrationSchema = MigrationSchemaBuilder::dropColumn($newColumnName);

        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'table_names' => $tableName,
            'UpMigrationSchema' => $upMigrationSchema,
            'DownMigrationSchema' => $downMigrationSchema,
        ]);

        $this->components->info('Migration file database/migrations/'.$migrationName.' created successfully.');
    }

    /**
     * Drop column from table
     *
     * @param string $tableName
     * @return void
     */
    private function dropColumn($tableName)
    {
        $existingColumns = collect(DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName))->map(function ($column) {
            return $column->getName();
        })->values();
        $columnName = $this->choice('Select column name to drop', $existingColumns->toArray(), 0);

        $dataType = collect(DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName))->get($columnName)->getType()->getName();

        $migrationName = date('Y_m_d_His').'_drop_'.$columnName.'_from_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-migration-stubs/migration.php', database_path('migrations/'.$migrationName));

        $upMigrationSchema = MigrationSchemaBuilder::dropColumn($columnName);
        $downMigrationSchema = MigrationSchemaBuilder::addColumn($columnName, $dataType);

        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'table_names' => $tableName,
            'UpMigrationSchema' => $upMigrationSchema,
            'DownMigrationSchema' => $downMigrationSchema,
        ]);

        $this->components->info('Migration file database/migrations/'.$migrationName.' created successfully.');
    }

    /**
     * Rename column in table
     *
     * @param string $tableName
     * @return void
     */
    private function renameColumn($tableName)
    {
        $existingColumns = collect(DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName))->map(function ($column) {
            return $column->getName();
        })->values();
        $columnName = $this->choice('Select column name to rename', $existingColumns->toArray(), 0);

        $newColumnName = $this->ask('Enter new column name');
        $newColumnName = Str::snake($newColumnName);

        $migrationName = date('Y_m_d_His').'_rename_'.$columnName.'_to_'.$newColumnName.'_in_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-migration-stubs/migration.php', database_path('migrations/'.$migrationName));

        $upMigrationSchema = MigrationSchemaBuilder::renameColumn($columnName, $newColumnName);
        $downMigrationSchema = MigrationSchemaBuilder::renameColumn($newColumnName, $columnName);

        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'table_names' => $tableName,
            'UpMigrationSchema' => $upMigrationSchema,
            'DownMigrationSchema' => $downMigrationSchema,
        ]);

        $this->components->info('Migration file : database/migrations/'.$migrationName.' created successfully.');
    }

    /**
     * Change column data type in table
     *
     * @param string $tableName
     * @return void
     */
    private function changeColumnDataType($tableName)
    {
        $existingColumns = collect(DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName))->map(function ($column) {
            return $column->getName();
        })->values();
        $columnName = $this->choice('Select column name to change data type', $existingColumns->toArray(), 0);
        $existingDataType = collect(DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName))->get($columnName)->getType()->getName();
        
        $newDataType = $this->choiceDataType();
        $additionalOptions = $this->choiceAdditionalOptions();

        $migrationName = date('Y_m_d_His').'_change_'.$columnName.'_data_type_in_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-migration-stubs/migration.php', database_path('migrations/'.$migrationName));

        $upMigrationSchema = MigrationSchemaBuilder::changeColumnDataType($columnName, $newDataType, $additionalOptions);
        $downMigrationSchema = MigrationSchemaBuilder::changeColumnDataType($columnName, $existingDataType);

        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'table_names' => $tableName,
            'UpMigrationSchema' => $upMigrationSchema,
            'DownMigrationSchema' => $downMigrationSchema,
        ]);

        $this->components->info('Migration file : database/migrations/'.$migrationName.' created successfully.');
    }

    /**
     * Manipulate multiple columns / custom schema
     *
     * @param string $tableName
     * @return void
     */
    private function manipulateMultipleColumns($tableName)
    {
        $migrationName = date('Y_m_d_His').'_update_columns_in_'.$tableName.'_table.php';
        (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
        copy(__DIR__.'/../../../database-migration-stubs/migration.php', database_path('migrations/'.$migrationName));

        $upMigrationSchema = '// Add your up migration schema here';
        $downMigrationSchema = '// Add your down migration schema here';

        $this->replaceContent(database_path('migrations/'.$migrationName), [
            'table_names' => $tableName,
            'UpMigrationSchema' => $upMigrationSchema,
            'DownMigrationSchema' => $downMigrationSchema,
        ]);

        $this->components->info('Migration file : database/migrations/'.$migrationName.' created successfully.');
    }

    /**
     * Choice data type
     *
     * @return string
     */
    private function choiceDataType()
    {
        return $this->choice('Select data type', [
            'custom',
            'bigIncrements',
            'bigInteger',
            'binary',
            'boolean',
            'char',
            'date',
            'dateTime',
            'dateTimeTz',
            'decimal',
            'double',
            'encrypted',
            'enum',
            'float',
            'geometry',
            'geometryCollection',
            'increments',
            'integer',
            'ipAddress',
            'json',
            'jsonb',
            'lineString',
            'longText',
            'macAddress',
            'mediumIncrements',
            'mediumInteger',
            'mediumText',
            'morphs',
            'uuidMorphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'rememberToken',
            'set',
            'smallIncrements',
            'smallInteger',
            'softDeletes',
            'softDeletesTz',
            'string',
            'text',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'timestamps',
            'timestampsTz',
            'tinyIncrements',
            'tinyInteger',
            'unsignedBigInteger',
            'unsignedDecimal',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'uuid',
            'year',
        ], 0);
    }

    /**
     * Choice additional options
     *
     * @return array
     */
    private function choiceAdditionalOptions()
    {
        return multiselect('Select additional options', [
            'after',
            'autoIncrement',
            'charset',
            'comment',
            'default',
            'first',
            'nullable',
            'unique',
        ]);
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
