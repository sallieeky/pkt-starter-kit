<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait ManipulateVueResource
{
    private Model $model;
    private string $nameArgument;

    protected function manipulateVueResource(Model $model, string $nameArgument)
    {
        $this->model = $model;
        $this->nameArgument = $nameArgument;

        $this->manipulateVuePage();
        $this->manipulateController();
        $this->manipulateRoute();
        $this->manipulatePermissions();
    }

    /**
     * Create Vue Page
     *
     * @return void
     */
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
        copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/IndexPage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Manage.vue'));

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
                    <el-input v-model=\"form$modelName.$column\" autocomplete=\"one-time-code\" autocorrect=\"off\" spellcheck=\"false\" />
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

        $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Manage.vue'), [
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

    /**
     * Create Controller
     *
     * @param string $file
     * @param array $replacements
     * @return void
     */
    private function manipulateController()
    {
        $nameArgument = $this->nameArgument;
        $modelName = $this->nameArgument;
        $modelLabel = Str::headline($nameArgument);

        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        copy(__DIR__.'/../../../resource-template/vue/app/Http/Controllers/ResourceController.php', app_path('Http/Controllers/' . $nameArgument . 'Controller.php'));

        $this->replaceContent(app_path('Http/Controllers/' . $nameArgument . 'Controller.php'), [
            'ModelLabel' => $modelLabel,
            'ModelName' => $modelName,
            'modelName' => Str::camel($modelName),
        ]);
    }

    /**
     * Create Route
     *
     * @return void
     */
    private function manipulateRoute()
    {
        $nameArgument = $this->nameArgument;
        $modelName = $this->nameArgument;
        $groupName = Str::lower(Str::snake($nameArgument));
        $route = Str::lower(Str::kebab($nameArgument));

        $primaryKey = $this->model->getKeyName();

        $route = "Route::controller(App\Http\Controllers\\{$modelName}Controller::class)->group(function () {
            Route::get('/$route', 'managePage')->name('$groupName.browse')->can('$groupName.browse');
            Route::get('/$route/data-processing', 'dataProcessing')->name('$groupName.data_processing')->can('$groupName.browse');
            Route::post('/$route', 'create')->name('$groupName.create')->can('$groupName.create');
            Route::put('/$route/{{$modelName}:$primaryKey}', 'update')->name('$groupName.update')->can('$groupName.update');
            Route::delete('/$route/{{$modelName}:$primaryKey}', 'delete')->name('$groupName.delete')->can('$groupName.delete');
        });";

        file_put_contents(base_path('routes/web.php'), $route, FILE_APPEND);
    }

    /**
     * Replace content in file
     *
     * @param string $file
     * @param array $replacements
     * @return void
     */
    private function manipulatePermissions()
    {
        $permissionsName = config('permissions');
        $nameArgument = $this->nameArgument;
        $groupName = Str::lower(Str::snake($nameArgument));
        
        // update permissions in config/permissions.php
        $permissionsName[] = [
            'group_name' => $groupName,
            'permissions' => ['browse', 'create', 'update', 'delete']
        ];

        file_put_contents(config_path('permissions.php'), $permissionsName);
    }
}
