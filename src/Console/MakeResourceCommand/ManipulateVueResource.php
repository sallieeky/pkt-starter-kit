<?php

namespace Pkt\StarterKit\Console\MakeResourceCommand;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Pkt\StarterKit\Utils\FormBuilder;

trait ManipulateVueResource
{
    private Model $model;
    private string $nameArgument;
    private bool $multiPage;

    protected function manipulateVueResource(Model $model, string $nameArgument, $multiPage = false)
    {
        $this->model = $model;
        $this->nameArgument = $nameArgument;
        $this->multiPage = $multiPage;

        // check if the vue page already exists
        $exist = (new Filesystem)->exists(resource_path('js/Pages/' . $nameArgument ));
        if ($exist && !$this->option('force')) {
            $this->components->error('Vue page already exists: ' . $nameArgument);
            return 0;
        }

        // check if the controller already exists
        $exist = (new Filesystem)->exists(app_path('Http/Controllers/' . $nameArgument . 'Controller.php'));
        if ($exist && !$this->option('force')) {
            $this->components->error('Controller already exists: ' . $nameArgument);
            return 0;
        }

        $this->manipulateVuePage();
        $this->manipulateController();
        $this->manipulateRequest();
        $this->manipulateRoute();
        $this->manipulateSidemenuItem();
        $this->manipulatePermissions();

        $this->components->info('You can run php artisan db:seed --class=RoleAndPermissionSeeder to update the permissions');
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

        $primaryKey = $model->getKeyName();
        $columns = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());

        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/' . $nameArgument ));
        if ($this->multiPage) {
            copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/ManagePage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Manage.vue'));
            copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/CreatePage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Create.vue'));
            copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/UpdatePage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Update.vue'));
        } else {
            copy(__DIR__.'/../../../resource-template/vue/resources/js/Pages/IndexPage.vue', resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Manage.vue'));
        }

        // ResourceTitle
        $resourceTitle = Str::headline($nameArgument) . ' Management';
        $modelLabel = Str::headline($nameArgument);

        // ColumnTableSlot
        $columnTableSlot = '';
        foreach ($columns as $column) {
            $label = Str::headline($column);
            if (
                $column === $primaryKey || 
                $column === 'created_at' || 
                $column === 'updated_at' || 
                $column === 'deleted_at' || 
                $column === 'created_by' ||
                $column === 'updated_by' ||
                Str::endsWith($column, '_uuid') || 
                Str::endsWith($column, '_id')
            ) {
                continue;
            }
            $columnTableSlot .= '<DxColumn data-field="'. $column . '" caption="' . $label .'" :allowHeaderFiltering="false" />' . PHP_EOL . '                ';
        }

        // ModalFormSlot
        $modalFormSlot = '';
        foreach ($columns as $column) {
            $type = $model->getConnectionResolver()->connection()->getSchemaBuilder()->getColumnType($model->getTable(), $column);
            $required = $model->getConnectionResolver()->connection()->getSchemaBuilder()->getConnection()->getDoctrineColumn($model->getTable(), $column)->getNotnull();
            $label = Str::headline($column);
            if (
                $column === $primaryKey || 
                $column === 'created_at' || 
                $column === 'updated_at' || 
                $column === 'deleted_at' || 
                $column === 'created_by' ||
                $column === 'updated_by' ||
                Str::endsWith($column, '_uuid') || 
                Str::endsWith($column, '_id')
            ) {
                continue;
            }

            $modalFormSlot .= FormBuilder::build($type, $modelName, $column, $label, $required);
        }

        // Permission
        $groupName = Str::lower(Str::snake($nameArgument));
        $actionPermission = "can('$groupName.update|$groupName.delete')";
        $createPermission = "can('$groupName.create')";
        $updatePermission = "can('$groupName.update')";
        $deletePermission = "can('$groupName.delete')";

        // Route
        $groupName = Str::lower(Str::snake($nameArgument));
        $routeBrowse = "route('{$groupName}.browse')";
        $routeCreatePage = "route('{$groupName}.create_page')";
        $routeCreate = "route('{$groupName}.create')";
        $routeUpdatePage = "route('{$groupName}.update_page', data$modelName.$primaryKey)";
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
            $defaultValue = $model->getConnectionResolver()->connection()->getSchemaBuilder()->getConnection()->getDoctrineColumn($model->getTable(), $column)->getDefault() ?? '';
            $formAddAction .= "form$modelName.$column = '$defaultValue';" . PHP_EOL . '    ';
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
            'RouteCreatePage' => $routeCreatePage,
            'RouteUpdatePage' => $routeUpdatePage,
            'RouteCreate' => $routeCreate,
            'RouteUpdate' => $routeUpdate,
            'RouteDelete' => $routeDelete,
            'RouteDataProcessing' => $routeDataProcessing,
            'FormUseForm' => $formUseForm,
            'FormAddAction' => $formAddAction,
            'FormEditAction' => $formEditAction,
        ]);

        if ($this->multiPage) {
            $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Create.vue'), [
                'ResourceTitle' => Str::headline($nameArgument) . ' Create',
                'ModelLabel' => $modelLabel,
                'ModelName' => $modelName,
                'modelName' => Str::camel($modelName),
                'PrimaryKey' => $primaryKey,
                'FormSlot' => $modalFormSlot,
                'RouteCreate' => $routeCreate,
                'RouteBrowse' => $routeBrowse,
                'FormUseForm' => $formUseForm,
                'FormAddAction' => $formAddAction,
            ]);

            $this->replaceContent(resource_path('js/Pages/' . $nameArgument . '/' . $nameArgument . 'Update.vue'), [
                'ResourceTitle' => Str::headline($nameArgument) . ' Update',
                'ModelLabel' => $modelLabel,
                'ModelName' => $modelName,
                'modelName' => Str::camel($modelName),
                'PrimaryKey' => $primaryKey,
                'FormSlot' => $modalFormSlot,
                'RouteUpdate' => $routeUpdate,
                'RouteBrowse' => $routeBrowse,
                'FormUseForm' => $formUseForm,
                'FormEditAction' => $formEditAction,
            ]);
        }

        $this->components->info('Vue page file resources/js/Pages/' . $nameArgument . '/' . $nameArgument . 'Manage.vue created successfully.');
        if ($this->multiPage) {
            $this->components->info('Vue page file resources/js/Pages/' . $nameArgument . '/' . $nameArgument . 'Create.vue created successfully.');
            $this->components->info('Vue page file resources/js/Pages/' . $nameArgument . '/' . $nameArgument . 'Update.vue created successfully.');
        }
    }

    /**
     * Create Controller
     *
     * @return void
     */
    private function manipulateController()
    {
        $nameArgument = $this->nameArgument;
        $modelName = $this->nameArgument;
        $modelLabel = Str::headline($nameArgument);

        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        if ($this->multiPage) {
            copy(__DIR__.'/../../../resource-template/vue/app/Http/Controllers/ResourceController.php', app_path('Http/Controllers/' . $nameArgument . 'Controller.php'));
        } else {
            copy(__DIR__.'/../../../resource-template/vue/app/Http/Controllers/ResourceSimpleController.php', app_path('Http/Controllers/' . $nameArgument . 'Controller.php'));
        }

        $this->replaceContent(app_path('Http/Controllers/' . $nameArgument . 'Controller.php'), [
            'ModelLabel' => $modelLabel,
            'ModelName' => $modelName,
            'modelName' => Str::camel($modelName),
        ]);

        $this->components->info('Controller app/Http/Controllers/' . $nameArgument . 'Controller.php created successfully.');
    }

    /**
     * Create Request
     *
     * @return void
     */
    private function manipulateRequest()
    {
        $modelName = $this->nameArgument;
        $modelLabel = Str::headline($modelName);

        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/' . $modelName));
        copy(__DIR__.'/../../../resource-template/vue/app/Http/Requests/CreateRequest.php', app_path('Http/Requests/' . $modelName . '/Create' . $modelName . 'Request.php'));
        copy(__DIR__.'/../../../resource-template/vue/app/Http/Requests/UpdateRequest.php', app_path('Http/Requests/' . $modelName . '/Update' . $modelName . 'Request.php'));

        // Rules
        $rules = '';
        $columns = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
        foreach ($columns as $column) {
            if (
                $column === $this->model->getKeyName() || 
                $column === 'created_at' || 
                $column === 'updated_at' || 
                $column === 'deleted_at' ||
                $column === 'created_by' ||
                $column === 'updated_by' ||
                Str::endsWith($column, '_uuid') || 
                Str::endsWith($column, '_id')
            ) {
                continue;
            }
            $required = $this->model->getConnectionResolver()->connection()->getSchemaBuilder()->getConnection()->getDoctrineColumn($this->model->getTable(), $column)->getNotnull() ? 'required' : null;
            if ($required) {
                $rules .= "'$column' => ['required'],\n" . '            ';
            }else{
                $rules .= "'$column' => ['nullable'],\n" . '            ';
            }
        }

        $this->replaceContent(app_path('Http/Requests/' . $modelName . '/Create' . $modelName . 'Request.php'), [
            'ModelLabel' => $modelLabel,
            'ModelName' => $modelName,
            'Rules' => $rules,
        ]);

        $this->replaceContent(app_path('Http/Requests/' . $modelName . '/Update' . $modelName . 'Request.php'), [
            'ModelLabel' => $modelLabel,
            'ModelName' => $modelName,
            'Rules' => $rules,
        ]);

        $this->components->info('Create Request file app/Http/Requests/' . $modelName . '/Create' . $modelName . 'Request.php created successfully.');
        $this->components->info('Update Request file app/Http/Requests/' . $modelName . '/Update' . $modelName . 'Request.php created successfully.');
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
        $modelNameCamel = Str::camel($this->nameArgument);
        $groupName = Str::lower(Str::snake($nameArgument));
        $route = Str::lower(Str::kebab(Str::plural($nameArgument)));

        $primaryKey = $this->model->getKeyName();

        if ($this->multiPage) {
            $route = "
Route::authenticated()
    ->prefix('/master/$route')
    ->name('$groupName.')
    ->controller(App\Http\Controllers\\{$modelName}Controller::class)->group(function () {
        Route::get('/', 'managePage')->name('browse')->can('$groupName.browse');
        Route::get('/data-processing', 'dataProcessing')->name('data_processing')->can('$groupName.browse');
        Route::get('/create', 'createPage')->name('create_page')->can('$groupName.create');
        Route::post('/create', 'create')->name('create')->can('$groupName.create');
        Route::get('/update/{{$modelNameCamel}:$primaryKey}', 'updatePage')->name('update_page')->can('$groupName.update');
        Route::put('/update/{{$modelNameCamel}:$primaryKey}', 'update')->name('update')->can('$groupName.update');
        Route::delete('/delete/{{$modelNameCamel}:$primaryKey}', 'delete')->name('delete')->can('$groupName.delete');
    });";
        } else {
        $route = "
Route::authenticated()
    ->prefix('/master/$route')
    ->name('$groupName.')
    ->controller(App\Http\Controllers\\{$modelName}Controller::class)->group(function () {
        Route::get('/', 'managePage')->name('browse')->can('$groupName.browse');
        Route::get('/data-processing', 'dataProcessing')->name('data_processing')->can('$groupName.browse');
        Route::post('/create', 'create')->name('create')->can('$groupName.create');
        Route::put('/update/{{$modelNameCamel}:$primaryKey}', 'update')->name('update')->can('$groupName.update');
        Route::delete('/delete/{{$modelNameCamel}:$primaryKey}', 'delete')->name('delete')->can('$groupName.delete');
    });";
        }
        

        file_put_contents(base_path('routes/web.php'), $route, FILE_APPEND);

        $this->components->info('Route file routes/web.php updated successfully.');
    }

    /**
     * Add to Sidemenuitem
     *
     * @return void
     */
    private function manipulateSidemenuItem()
    {
        $modelName = $this->nameArgument;
        $label = Str::headline($modelName);
        $route = Str::lower(Str::kebab(Str::plural($modelName)));
        $permission = Str::lower(Str::snake($modelName)) . '.browse';

        $filePath = resource_path('js/Core/Config/SidemenuItem.js');
        $navItemsJsString = file_get_contents($filePath);

        if ($navItemsJsString === false) {
            $this->components->error('File resources/js/Core/Config/SidemenuItem.js not found');
            return;
        }

        // Add quotes around object keys
        $navItemsJsString = preg_replace('/(\w+):/i', '"$1":', $navItemsJsString);

        // Remove the JavaScript specific parts
        $navItemsJsString = preg_replace('/export const navItems = |;/', '', $navItemsJsString);

        // Remove trailing commas before closing braces
        $navItemsJsString = preg_replace('/,\s*(\]|\})/', '$1', $navItemsJsString);

        $navItems = json_decode($navItemsJsString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->components->error('SidemenuItem file resources/js/Core/Config/SidemenuItem.js failed to update. Please ensure file has correct format.');
            return;
        }

        // Modify the array
        $masterDataFound = false;
        $userManagementIndex = null;

        foreach ($navItems as $index => $item) {
            if ($item['label'] === 'Master Data') {
                $masterDataFound = true;
            }
            if ($item['label'] === 'User Management') {
                $userManagementIndex = $index;
            }
        }
        // Add "Master Data" before "User Management" if it does not exist
        if (!$masterDataFound) {
            $newItem = [
                "label" => "Master Data",
                "href" => "/master-data",
                "icon" => "database",
                "submenu" => [
                    [
                        "label" => $label,
                        "href" => '/master/'.$route,
                        "permission" => $permission
                    ]
                ]
            ];

            if ($userManagementIndex !== null) {
                array_splice($navItems, $userManagementIndex, 0, [$newItem]);
            } else {
                $navItems[] = $newItem; // Add to the end if "User Management" is not found
            }
        } else {
            // If "Master Data" exists, ensure the submenu is correct
            foreach ($navItems as &$item) {
                if ($item['label'] === 'Master Data') {
                    $submenuExists = false;
                    foreach ($item['submenu'] as $submenu) {
                        if ($submenu['label'] === $label) {
                            $submenuExists = true;
                            break;
                        }
                    }
                    if (!$submenuExists) {
                        $item['submenu'][] = [
                            "label" => $label,
                            "href" => '/master/'.$route,
                            "permission" => $permission
                        ];
                    }
                    break;
                }
            }
        }

        $updatedNavItemsJson = json_encode($navItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($updatedNavItemsJson === false) {
            $this->components->error('SidemenuItem file resources/js/Core/Config/SidemenuItem.js failed to update. Please ensure file has correct format.');
        }

        // Prepare the JavaScript export string
        $updatedNavItemsJsString = "export const navItems = " . $updatedNavItemsJson . ";";

        // Write the updated content back to the JavaScript file
        file_put_contents($filePath, $updatedNavItemsJsString);

        $this->components->info('SidemenuItem file resources/js/Core/Config/SidemenuItem.js updated successfully.');
    }

    /**
     * Create Permissions
     *
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

        $content = "<?php\n\n// Define permissions name and permissions group with snake case\nreturn [\n";
        foreach ($permissionsName as $permission) {
            $content .= "    [\n";
            $content .= "        'group_name' => '{$permission['group_name']}',\n";
            $content .= "        'permissions' => [\n";
            foreach ($permission['permissions'] as $perm) {
                $content .= "            '$perm',\n";
            }
            $content .= "        ]\n";
            $content .= "    ],\n";
        }

        $content .= "];";

        file_put_contents(config_path('permissions.php'), $content);

        $this->components->info('Permissions file config/permissions.php updated successfully.');
    }
}
