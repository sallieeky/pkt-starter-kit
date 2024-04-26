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

## Additional Command

### Make resource command
This command will help you to create basic single page <strong>CRUD</strong> by only execute 1 command

```cmd
php artisan pkt:make-resource ModelName <additional-flag>
```

Additional flag you can use <br>
- `--force` if you want to force create the resource. <br> <strong>(CAREFULLY)</strong> This will replace all the existing related file.


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

## Helpers

### Leader API Integration
You can consume PKT Leader API by using helper from this library to get list of all employee, work unit, and PLT.

First you need to setup **.env** file and add this line
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
