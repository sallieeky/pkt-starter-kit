<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command implements PromptsForMissingInput
{
    use ManipulateVueResource;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pkt:make-resource {name : The name of the resource}
            {--force : Overwrite existing files}
            {--test : Create test cases}
            {--generate : Generate the resource with default values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource with base template';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        // make sure the user already run pkt:install
        if (!file_exists(resource_path('js/Core/Config/SidemenuItem.js')) && !file_exists(config_path('permissions.php'))) {
            $this->error('Please run php artisan pkt:install first');
            return 0;
        }

        // make sure argument name capitalized
        $nameArgument = ucfirst($this->argument('name'));
        try {
            $model = app('App\\Models\\' . $nameArgument);
        } catch (\Exception $e) {
            $this->error('Model not found: ' . $nameArgument);
            return 0;
        }

        $this->manipulateVueResource($model, $nameArgument);
        $this->createTestCases($model, $nameArgument);
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

    /**
     * Create test cases
     * 
     * @param string $model
     * @param string $nameArgument
     * @return void
     */
    protected function createTestCases($model, $nameArgument)
    {
        $exist = (new Filesystem)->exists(base_path('tests/Feature/'.$nameArgument));
        if ($exist && !$this->option('force')) {
            $this->error('Test already exists: tests/Feature/'.$nameArgument);
            return 0;
        }

        if ($exist && $this->option('force')) {
            (new Filesystem)->deleteDirectory(base_path('tests/Feature/'.$nameArgument));
        }

        if (!$this->option('test')) {
            return 0;
        }

        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature/'.$nameArgument));
        copy(__DIR__.'/../../../resource-template/default/tests/BrowseTest.php', base_path('tests/Feature/'.$nameArgument.'/Browse'.$nameArgument.'Test.php'));
        copy(__DIR__.'/../../../resource-template/default/tests/CreateTest.php', base_path('tests/Feature/'.$nameArgument.'/Create'.$nameArgument.'Test.php'));
        copy(__DIR__.'/../../../resource-template/default/tests/UpdateTest.php', base_path('tests/Feature/'.$nameArgument.'/Update'.$nameArgument.'Test.php'));
        copy(__DIR__.'/../../../resource-template/default/tests/DeleteTest.php', base_path('tests/Feature/'.$nameArgument.'/Delete'.$nameArgument.'Test.php'));

        $replacements = [
            'modelname' => Str::lower(Str::headline($nameArgument)),
            'model_name' => Str::snake($nameArgument),
            'ModelName' => $nameArgument,
            'Model Name' => Str::headline($nameArgument),
            'primary_key' => $model->getKeyName(),
            'table_names' => $model->getTable(),
        ];

        $files = [
            base_path('tests/Feature/'.$nameArgument.'/Browse'.$nameArgument.'Test.php'),
            base_path('tests/Feature/'.$nameArgument.'/Create'.$nameArgument.'Test.php'),
            base_path('tests/Feature/'.$nameArgument.'/Update'.$nameArgument.'Test.php'),
            base_path('tests/Feature/'.$nameArgument.'/Delete'.$nameArgument.'Test.php'),
        ];

        foreach ($files as $file) {
            $this->replaceContent($file, $replacements);
        }

        $this->info('Test cases created: tests/Feature'.$nameArgument);
    }
}
