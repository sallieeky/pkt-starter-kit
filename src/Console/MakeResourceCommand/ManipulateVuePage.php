<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait ManipulateVuePage
{
    private Model $model;
    private string $nameArgument;

    protected function manipulateVueResource(Model $model, string $nameArgument)
    {
        $this->model = $model;
        $this->nameArgument = $nameArgument;

        $this->manipulateVuePage();
    }

    private function manipulateVuePage()
    {
        $nameArgument = $this->nameArgument;
        $model = $this->model;

        $folderExists = (new Filesystem)->exists(resource_path('js/Pages/' . $nameArgument ));
        if ($folderExists && !$this->option('force')) {
            $this->error('Folder already exists: ' . $nameArgument);
            return 0;
        }

        $columns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());

        // ColumnTableSlot
        $columnTableSlot = '';
        foreach ($columns as $column) {
            $columnTableSlot .= '<DxColumn data-field="'. $column . '" caption="' . ucfirst($column) .'" :allowHeaderFiltering="false" />' . PHP_EOL . '                ';
        }

        $this->components->task('Creating views/pages...', function () use ($nameArgument, $columnTableSlot) {
            (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/' . $nameArgument ));
            copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/IndexPage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . '.vue'));

            $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . '.vue'), [
                'ColumnTableSlot' => $columnTableSlot,
            ]);
        });
    }
}
