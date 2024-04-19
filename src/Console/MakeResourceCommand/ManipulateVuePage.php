<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

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

        $modelName = $this->nameArgument;

        $folderExists = (new Filesystem)->exists(resource_path('js/Pages/' . $nameArgument ));
        if ($folderExists && !$this->option('force')) {
            $this->error('Folder already exists: ' . $nameArgument);
            return 0;
        }

        $primaryKey = $model->getKeyName();
        $columns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());

        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/' . $nameArgument ));
        copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/IndexPage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . '.vue'));

        // ResourceTitle
        $resourceTitle = ucfirst($nameArgument) . ' Management';
        $modelLabel = Str::headline($nameArgument);

        // ColumnTableSlot
        $columnTableSlot = '';
        foreach ($columns as $column) {
            $columnTableSlot .= '<DxColumn data-field="'. $column . '" caption="' . ucfirst($column) .'" :allowHeaderFiltering="false" />' . PHP_EOL . '                ';
        }

        // ModalFormSlot
        $modalFormSlot = '';
        foreach ($columns as $column) {
            $modalFormSlot .= "<el-form-item :error=\"getFormError('$column')\" prop=\"$column\" label=\"$column\" :required=\"true\">
                    <el-input v-model=\"formUser.$column\" autocomplete=\"one-time-code\" autocorrect=\"off\" spellcheck=\"false\" />
                </el-form-item>" . PHP_EOL . '                ';
        }

        // Permission
        $groupName = Str::lower(Str::snake($nameArgument));
        $actionPermission = "can('$groupName.update|$groupName.delete')";
        $createPermission = "can('$groupName.create')";
        $updatePermission = "can('$groupName.update')";
        $deletePermission = "can('$groupName.delete')";

        // Route
        $groupName = Str::lower(Str::snake($nameArgument));
        $routeCreate = "route('{$groupName}.create')";
        $routeUpdate = "route('{$groupName}.update', form$modelName.$primaryKey)";
        $routeDelete = "route('{$groupName}.delete', data$modelName.$primaryKey)";
        $routeDataProcessing = "route('{$groupName}.data_processing')";

        // Form useForm
        $formUseForm = '';
        foreach ($columns as $column) {
            $formUseForm .= $column . ": ''," . PHP_EOL . '    ';
        }

        // Form Add Action
        $formAddAction = '';
        foreach ($columns as $column) {
            $formAddAction .= "form$modelName.$column = '';" . PHP_EOL . '    ';
        }

        // Form Edit Action
        $formEditAction = '';
        foreach ($columns as $column) {
            $formEditAction .= "form$modelName.$column = data$modelName.$column;" . PHP_EOL . '    ';
        }

        $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . '.vue'), [
            'ResourceTitle' => $resourceTitle,
            'ModelLabel' => $modelLabel,
            'ModelName' => $modelName,
            'modelName' => Str::camel($modelName),
            'PrimaryKey' => $primaryKey,
            'ColumnTableSlot' => $columnTableSlot,
            'ActionPermission' => $actionPermission,
            'CreatePermission' => $createPermission,
            'UpdatePermission' => $updatePermission,
            'DeletePermission' => $deletePermission,
            'ModalFormSlot' => $modalFormSlot,
            'RouteCreate' => $routeCreate,
            'RouteUpdate' => $routeUpdate,
            'RouteDelete' => $routeDelete,
            'RouteDataProcessing' => $routeDataProcessing,
            'FormUseForm' => $formUseForm,
            'FormAddAction' => $formAddAction,
            'FormEditAction' => $formEditAction,
        ]);
    }
}
