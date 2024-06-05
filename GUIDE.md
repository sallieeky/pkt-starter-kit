# Development Guide
Before using the application, ensure that you have installed the starter kit.

```
php artisan pkt:install vue

#or

php artisan pkt:install react
```

## User
By default existing user for superadmin you can adjust in `database/seeders/UserSeeder.php`. <strong>It's highly recommended to change the credential</strong> 
```
username : superadmin
password : password
```

The default schema for users table in `database/migrations/2014_10_12_000000_create_users_table.php`
```
Schema::create('users', function (Blueprint $table) {
    $table->id('user_id');
    $table->uuid('user_uuid');
    $table->string('username')->unique(); // [used in local/ldap/sso portal authentication]
    $table->string('name');
    $table->string('npk')->unique()->nullable();
    $table->string('email')->unique()->nullable();
    $table->string('password'); // [used in local/ldap/sso portal authentication]
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

## Manage Permissions
Role and permission installed in this starter kit supported by [Spatie laravel permission](https://spatie.be/docs/laravel-permission/v6/introduction).

By default there are only 2 role Superadmin and Viewer, you can adjust the permission in `config/permissions.php`

```php
return [
    [
        'group_name' => 'user',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
        ]
    ],
    [
        'group_name' => 'role',
        'permissions' => [
            'browse',
            'create',
            'update',
            'delete',
            'assign_permission',
        ]
    ],
    [
        'group_name' => 'user_log',
        'permissions' => [
            'browse',
        ]
    ],
    ... (additional permission)
];
```

If you add or update the permission, don't forget to run command to seed the role and permision
```cmd
php artisan db:seed --class=RoleAndPermissionSeeder
```

To check the permission with PHP example
```php
auth()->user()->can('user.browse');
auth()->user()->canAny(['role.assign_permission', 'user_log.browse']);
```

To check the permission with JS example
```js
import { can } from '@/Core/Helpers/permission-check';

can('user.browse')
can('role.assign_permission|user_log.browse')
can(['role.assign_permission', 'user_log.browse'])
```

## Manage Page
By default `User management` is installed in this starter kit. If you want to add new page, you need to add file in `resources/js/pages/[PageName]`

If you need to add the page to sidebar you can add in `resources/js/Core/Config/SidemenuItem.js`
```js
export const navItems = [
    {
        label: "Dashboard",
        href: "/dashboard",
        icon: "home"
    },
    {
        label: "User Management",
        href: "/users",
        icon: "users",
        permission: "user.browse | role.browse | user_log.browse",
        submenu:[
            {
                label: "User",
                href: "/user",
                permission: "user.browse",
            },
            {
                label: "Role & Permission",
                href: "/role-and-permission",
                permission: "role.browse",
            },
            {
                label: "User Log",
                href: "/user-log",
                permission: "user_log.browse",
            },
        ]
    },
    ... (additional page)
];
```

## Manage Global Search
<img src="/art/GlobalSearch.png" alt="Global Search">

You can use global search functionallity by adding `GlobalSearch` trait in your `Model`. By default User model has already set for global search, so you can check how to use from it.

**Example**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Pkt\StarterKit\Traits\GlobalSearch;
use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    use HasFactory, GlobalSearch;

    /**
     * Get the columns that can be searched.
     *
     * @return array
     */
    public function searchableAttributes(): array
    {
        return [
            'name',
            'guard',
        ];
    }

    /**
     * Get the columns id that can be searched.
     *
     * @return int|string
     */
    public function searchableAttributeId(): int|string
    {
        return $this->role_id;
    }

    /**
     * Get action url for searchable record.
     *
     * @return ?string
     */
    public function searchableRecordActionUrl($record): ?string
    {
        return route('role.browse');
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return string
     */
    public function searchableFormatRecord($record): string
    {
        return $record->name . ' ('. $record->users->count() . ' users)';
    }

    /**
     * Get the eloquent query.
     *
     * @return ?object
     */
    public function searchableEloquentQuery(): ?object
    {
        return $this->query()
            ->where('active', true);
    }
}
```
- `searchableAttributes(){}` used to define your model column that can be search. By default it refer to `all` columns
- `searchableAttributeId(){}` used to define your model unique identifier. By default it refer to `id` column
- `searchableRecordActionUrl($record){}` used to define action url when you click on the record from your search. By default it's `null`
- `searchableFormatRecord($record){}` used to format the data shown when you search. By default it's refer to `id` column
- `searchableEloquentQuery(){}` used to customize your model query if you need an advance query

## Additional Command

### 1. Make resource command
This command will help you to create basic single page <strong>CRUD</strong> by only execute 1 command

```cmd
php artisan pkt:make-resource ModelName <additional-flag>
```

Additional flag you can use <br>
- `--force` if you want to force create the resource. <br> <strong>(CAREFULLY)</strong> This will replace all the existing related file.
- `--test` if you want to automatic create test case for your resource including CRUD test.


This command will help you to create and optimize some file such as
```
Vue page : resources/js/pages/[ModelName]/[ModelName]Manage.vue
Controller : app/Http/Controllers/[ModelName]Controller.php
Create Request : app/Http/Requests/[ModelName]/Create[ModelName]Request.php
Update Request : app/Http/Requests/[ModelName]/Update[ModelName]Request.php
Route : routes/web.php
Side menu item : resources/js/Core/Config/SidemenuItem.js
Permission : config/permissions.php
```

<strong>(RECOMMENDED ACTION)</strong><br>
After you run the command, it's recommended to re-seed the role and permission to update permission to database
```cmd
php artisan db:seed --class=RoleAndPermissionSeeder
```

### 2. Make Blank Page Command
This command will generate blank page file for your frontend
```cmd
php artisan pkt:make-page <Filepath/Filename>
```

#### Example
```cmd
php artisan pkt:make-page MasterData/Equipment
```

this command will generate file `resources/js/Pages/MasterData/Equipment.vue`

### 3. Make Blank Component Command
This command will generate blank component file for your frontend
```cmd
php artisan pkt:make-component <Filepath/Filename>
```

#### Example
```cmd
php artisan pkt:make-component Dashboard/AdminTab
```

this command will generate file `resources/js/Components/Dashboard/AdminTab.vue`

### 4. Init Leader Command
First you need to setup `.env` file and add this line.
```.env
LEADER_API_KEY=<ask admin>
```

If you already set `LEADER_API_KEY` to your `.env` file, than run this command.
```cmd
php artisan pkt:leader-init
```
This command will initialize and setup users table to sync with PKT Leader user, it also will **syncronize** or **create new** user from PKT Leader

```
(additional column)

hierarchy_code  => string
position_id     => string
position        => string
work_unit_id    => string
work_unit       => string
users_flag      => string
```

### 5. Sync Leader Command
First you need to setup `.env` file and add this line.
```.env
LEADER_API_KEY=<ask admin>
```

If you already set `LEADER_API_KEY` to your `.env` file, than run this command.
```cmd
php artisan pkt:leader-sync
```
This command will sync users from PKT Leader

### 6. Make Database Table
This command will create new `migration` and `model` for your database table including transaction (tr), master (ms), or value list (vl)
```cmd
php artisan pkt:make-table <ModelName>
```
**(RECOMMENDED)**
Use singular for ModelName and **don't add** `Tr` or `Ms` or `Vl` to ModelName

#### Example
If you want to make new master data table for Employee, you can execute command
```cmd
php artisan pkt:make-table Employee

# choose
Select table type [transaction (tr)]:
  [0] transaction (tr)
  [1] master (ms)
  [2] value list (vl)
 > master (ms)
```

this will create new migration and model
```
app/Models/Employee
database/migrations/...create_ms_employees_table.php
database/factories/EmployeeFactory.php
```

### 7. Make Database View Table

This command will create new `migration` and `model` for your database view table
```cmd
php artisan pkt:make-view <ViewModelName> --model=<RelatedModel>
```

#### Example
If you want to make view for active user, you can execute command
```cmd
php artisan pkt:make-view VwActiveUser --model=User
```
this command wil generate
```
app/Models/Views/VwActiveUser.php
database/migrations/..._create_vw_active_users_view.php
```

Inside file **database/migrations/..._create_vw_active_users_view.php**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Staudenmeir\LaravelMigrationViews\Facades\Schema as FacadesSchema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $query = \App\Models\User::query()
            ->where('is_active', true);
        FacadesSchema::createView('vw_active_users', $query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        FacadesSchema::dropViewIfExists('vw_active_users');
    }
};
```

Then migrate your database
```cmd
php artisan migrate
```

## Helpers

### Leader API Integration
You can consume PKT Leader API by using helper from this library to get list of all employee, work unit, and PLT.

First you need to setup `.env` file and add this line
```.env
LEADER_API_KEY=<ask admin>
```

And to use the helper
```php
use Pkt\StarterKit\Helpers\LeaderApi;

$employee = LeaderApi::getAllEmployee();
$workUnit = LeaderApi::getAllWorkUnit();
$plt = LeaderApi::getAllPlt();
```

### Notification

<img src="/art/Notification.png" alt="Notification">

```php
use Pkt\StarterKit\Notifications\Notification;

$user = Auth::user();
$user->notify(new Notification('title', 'message', '/url'));
```

By default notification is not <b>real time</b>, to enable real time functional support by <b>Laravel Reverb</b> you can enable it by change the `BROADCAST_DRIVER` from **log** to **reverb** in `.env` file.

```env
...
BROADCAST_DRIVER=reverb
...
```

Then run command in your terminal
```cmd
php artisan reverb:start
```