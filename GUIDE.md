# Development Guide
Before using the application, ensure that you have installed the starter kit.

```
php artisan pkt:install vue

#or

php artisan pkt:install react
```

## Class/File Naming Standard

For better development proccees we need to standard the naming so it will help us to easily search and understand the class or file purpose. The naming also important on this package for example for the database interaction that the database name should be plural and the model should be singular.

- **Database Table**, the naming for database table should be `plural` with `snake_case` and `lower case`, it also recommended to use english for the table name. The naming should define the type of the table using `ms`, `tr`, or `vl` on the front of the name.
```php
# Example for table incoming transaction
Schema::create('tr_incoming_transactions', function (Blueprint $table) {...}

# Example for table PI transaction
Schema::create('tr_pi_transactions', function (Blueprint $table) {...}

# Example for table material
Schema::create('ms_materials', function (Blueprint $table) {...}
```

- **Model**, the naming for model should be `singular` with `PascalCase`. And if there's abbreviation like PKT, PI, SPK, etc, just the first letter that using the capital.
```php
# Example for table incoming_transactions
class IncomingTransaction
{
    // ...
}

# Example for table pi_transactions
class PiTransaction
{
    // ...
}
```

- **Table attribute**, the naming for attribute/column on your table should using `snake_case` and all with `lower case`, it also recommended to use english for the attribute name.
```php
$table->string('name');
$table->foreignIdFor(\App\Models\User::class, 'user_id');
$table->foreignIdFor(\App\Models\User::class, 'created_by');
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
import { can, canAny, canAll } from '@/Core/Helpers/permission-check';

can('user.browse'); // should have the permission provide
can('role.assign_permission|user_log.browse'); // should have any of the permission provide
canAny(['role.assign_permission', 'user_log.browse']); // should have any the permission provide
canAll(['role.assign_permission', 'user_log.browse']); // should have all the permission provide

import { hasRole, hasAnyRole, hasAllRole } from '@/Core/Helpers/role-check';

hasRole('admin'); // should have the role provide
hasRole('admin|user'); // should have any role provide
hasAnyRole(['admin','user']); // should have any role provide
hasAllRole(['admin','user']); // should have all role provide
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
        label: "Master Data",
        type: "header",
        permission: "user.browse | role.browse | user_log.browse"
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
    {
        label: "Personalization",
        type: "fixed-header",
    },
    {
        label: "Notification",
        href: "/notification",
        icon: "bell",
        type: "fixed",
    },
    {
        label: "Setting",
        href: "/setting",
        icon: "cog-6-tooth",
        type: "fixed",
    },
    ... (additional page)
];
```

You can add the parameter below.
- `label` to define the text.
- `href` to define url when menu clicked.
- `icon` to define the icon, this refer to [Heroicons](https://heroicons.com/).
- `permission` to define the permission needed to show the menu.
- `type` to define the type between `header`, `fixed`, `header-fixed`.

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
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @return ?string
     */
    public function searchableRecordActionUrl($record): ?string
    {
        return route('role.browse');
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
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

### Make Resource
This command will help you to create basic single page <strong>CRUD</strong> by only execute 1 command

```cmd
php artisan pkt:make-resource ModelName <additional-flag>
```

Additional flag you can use <br>
- `--multi-page` if you want to make create and update in a new page. By default resource only using 1 page with create and update using modal.
- `--test` if you want to automatic create test case for your resource including CRUD test.
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

### Make Blank Page
This command will generate blank page file for your frontend
```cmd
php artisan pkt:make-page <Filepath/Filename>
```

#### Example
```cmd
php artisan pkt:make-page MasterData/Equipment
```

this command will generate file `resources/js/Pages/MasterData/Equipment.vue`

### Make Component
This command will generate blank component file for your frontend
```cmd
php artisan pkt:make-component <Filepath/Filename>
```

#### Example
```cmd
php artisan pkt:make-component Dashboard/AdminTab
```

this command will generate file `resources/js/Components/Dashboard/AdminTab.vue`

### Make Widget
Widget is almost the same with component, but it's commonly used only once and for specific page for example chart, table, form, etc, but you can still reuse it on other page if needed.
```cmd
php artisan pkt:make-widget <Filepath/Filename>
```

#### Example
```cmd
php artisan pkt:make-widget Dashboard/MonthlyProductionChart

<!-- Question -->
Select type of Widget [Blank Widget]:
  [0] Blank Widget
  [1] Table Widget
  [2] Form Widget
  [3] Statistic Widget
  [4] Bar/Column Chart Widget
  [5] Line Chart Widget
  [6] Pie Chart Widget
  [7] Donut Chart Widget
 > 4
```

this command will generate file `resources/js/Widgets/Dashboard/MonthlyProductionChart.vue`.

#### Blank Widget
<img src="/art/Widgets/BlankWidget.png" alt="Blank Widget">

#### Table Widget
<img src="/art/Widgets/TableWidget.png" alt="Table Widget">

#### Form Widget
<img src="/art/Widgets/FormWidget.png" alt="Form Widget">

#### Statistic Widget
<img src="/art/Widgets/StatisticWidget.png" alt="Statistic Widget">

#### Bar/Column Chart Widget
<img src="/art/Widgets/BarChartWidget.png" alt="Bar/Column Chart Widget">

#### Line Chart Widget
<img src="/art/Widgets/LineChartWidget.png" alt="Line Chart Widget">

#### Pie Chart Widget
<img src="/art/Widgets/PieChartWidget.png" alt="Pie Chart Widget">

#### Donut Chart Widget
<img src="/art/Widgets/DonutChartWidget.png" alt="Donut Chart Widget">


### Init Leader
First you need to setup `.env` file and add this line.
```.env
LEADER_API_KEY=<ask admin>
```

If you already set `LEADER_API_KEY` to your `.env` file, than run this command.
```cmd
php artisan pkt:leader-init <additional-flag>
```
Additional flag you can use <br>
- `--add-dx-column` Add additional dx column to UserManage.vue that related with leader.


This command will initialize and setup users table to sync with PKT Leader user, it also will **syncronize** or **create new** user from PKT Leader

```
(additional column)

hierarchy_code  => string
position_id     => string
position        => string
work_unit_id    => string
work_unit       => string
user_flag       => string
user_alias      => string
```

### Sync Leader
First you need to setup `.env` file and add this line.
```.env
LEADER_API_KEY=<ask admin>
```

If you already set `LEADER_API_KEY` to your `.env` file, than run this command.
```cmd
php artisan pkt:leader-sync
```
This command will sync users from PKT Leader

### Make Database Table
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

### Make Database View Table

This command will create new `migration` and `model` for your database view table
```cmd
php artisan pkt:make-view <ViewModelName> <additional-flag>
```

Additional flag you can use <br>
- `--model=<RelatedModel>` you can predefined the related model for your view table.
- `--raw` if you want to use raw query for your creating view method.


#### Example
If you want to make view for active user, you can execute command.
```cmd
php artisan pkt:make-view VwActiveUser --model=User

<!-- Question -->
 Select the creation type of view table [eloquent]:
  [0] eloquent
  [1] raw
 > 0
```
this command wil generate.
```
app/Models/Views/VwActiveUser.php
database/migrations/..._create_vw_active_users_view.php
```

- Inside file `database/migrations/..._create_vw_active_users_view.php` if you using `eloquent` type.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Staudenmeir\LaravelMigrationViews\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        Schema::dropViewIfExists('vw_active_users');

        /**
         * ============================================
         * Create the view with the given query.
         * ============================================
         */
        $query = \App\Models\User::query()
            ->where('is_active', true);
        Schema::createView('vw_active_users', $query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        Schema::dropViewIfExists('vw_active_users');
    }
};
```

- Inside file `database/migrations/..._create_vw_active_users_view.php` if you using `raw` type.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        DB::statement('DROP VIEW IF EXISTS vw_active_users');

        /**
         * ============================================
         * Create the view with the given query.
         * ============================================
         */
        DB::statement("
            CREATE VIEW vw_active_users AS
            (
                select * from users
                where is_active = true
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        DB::statement('DROP VIEW IF EXISTS vw_active_users');
    }
};
```

Then migrate your database
```cmd
php artisan migrate
```

### Make Database Migration

Sometimes we need to manipulate existing table on database such as **adding new column, rename column, change column data type, and drop column**. Basically you can directly manipulate the table from database client such as DBeaver, SQL Server Management Studio (SSMS), HeidiSQL, etc. But for the best practice to manipulate database table is using migration so all database changes will be tracked and easily to audit. So there is additional command that help you automatically generate a migration with standard name convention by using this command.

```cmd
php artisan pkt:make-migration
```

After you run the command, it will ask you several question based on your case. Example we want to add a new column named birth with date data type to users table.

```cmd
php artisan pkt:make-migration

<!-- Question -->
Select migration type [add column]:
  [0] add column
  [1] drop column
  [2] rename column
  [3] change column data type
  [4] manipulate multiple columns / custom schema
> 0

<!-- Question -->
Select table name [active_users_view]:
  ...
  [3] users
  ...
> 3

<!-- Question -->
Enter new column name:
> birth 

<!-- Question -->
Select data type [custom]:
  [0 ] custom
  [1 ] bigIncrements
  [2 ] bigInteger
  [3 ] binary
  [4 ] boolean
  [5 ] char
  [6 ] date
  [7 ] dateTime
  [8 ] dateTimeTz
  [9 ] decimal
  [10] double
  [11] enum
  [12] float
  [13] geometry
  [14] geometryCollection
  [15] increments
  [16] integer
  [17] ipAddress
  [18] json
  [19] jsonb
  [20] lineString
  [21] longText
  [22] macAddress
  [23] mediumIncrements
  [24] mediumInteger
  [25] mediumText
  [26] morphs
  [27] uuidMorphs
  [28] multiLineString
  [29] multiPoint
  [30] multiPolygon
  [31] nullableMorphs
  [32] nullableUuidMorphs
  [33] point
  [34] polygon
  [35] rememberToken
  [36] set
  [37] smallIncrements
  [38] smallInteger
  [39] softDeletes
  [40] softDeletesTz
  [41] string
  [42] text
  [43] time
  [44] timeTz
  [45] timestamp
  [46] timestampTz
  [47] timestamps
  [48] timestampsTz
  [49] tinyIncrements
  [50] tinyInteger
  [51] unsignedBigInteger
  [52] unsignedDecimal
  [53] unsignedInteger
  [54] unsignedMediumInteger
  [55] unsignedSmallInteger
  [56] unsignedTinyInteger
  [57] uuid
  [58] year
> 6

<!-- Question -->
Select additional options ──────────────────────────────────────┐
 │ › ◻ nullable                                                 │
 │   ◻ unique                                                   │
 │   ◻ default                                                  │
 └──────────────────────────────────────────────────────────────┘

<!-- Output -->
Migration file : 2024_06_15_220000_add_birth_to_users_table.php
Migration file created successfully
```

### Model Sync

Sometimes you want to interact with existing database table from your new apps or existing apps that not using model to do the query, so you need to make all the model that related to that table manually. So here we provide the command to automatically sync the model with the database table, if the model that related to the table is not exist, it will create a new model, but if the model is already exist it will pass.

```cmd
php artisan pkt:model-sync <additional-flag>
```

Additional flag you can use <br>
- `--table=<table_name>` if you want to sync with specific existing table.

```cmd
php artisan pkt:model-sync

<!-- Question -->
Do you want to sync all table and model or just one table? [All Tables]:
  [0] All Tables
  [1] One Table
 > 0

<!-- Output -->
+-----------+---------+--------------------------------+
| Table     | Status  | Model                          |
+-----------+---------+--------------------------------+
| tr_issues | success | exists at app/Models/Issue.php |
| users     | failed  | exists at app/Models/User.php  |
+-----------+---------+--------------------------------+
```

### Init and Make Mail

If you need to use mail on your application then you want to easily configure the template of your email, you can run command to init mail using.

```cmd
php artisan pkt:mail-init
```

This command will automatically creating some components you need on `resources/views/components/mails`. You can change these files if you need to optimize the style of your email.

Additionally, if you want to generate a mail class that automatically integrated with the mail component, you can make using this command.
```cmd
php artisan pkt:make-mail <MailName>
```

This command will generate `app/Mail/MailName.php` and `resource/views/mails/mail-name.blade.php`.

#### How to send mail

There are two option if you want to send email, you can direct send the email and queue the email first then run the queue which is the recommended way to send an email. 

When you wanna using queue to send email, first you need to change the `QUEUE_CONNECTION` to `database` and run command `php artisan queue:table` and migrate `php artisan migrate`, if you wanna run the queue you can run command `php artisan queue:work`.
```env
...
QUEUE_CONNECTION=database
...
```

**Example**

We want to send Need Approval mail to the AVP to notify the AVP that there is issue that need to be approve.

```cmd
php artisan pkt:make-mail NeedApprovalMail
```

Then we need to costumize the `app/Mail/NeedApproval.php`.

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NeedApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $issue
    ){}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Approval Issue ' . $this->issue->issue_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.need-approval-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->issue?->mediaEvidences()->get()->makeVisible(['path']) as $media) {
            $attachments[] = Attachment::fromStorage('public/'.$media->path);
        }
        foreach ($this->issue?->mediaReport()->get()->makeVisible(['path']) as $media) {
            $attachments[] = Attachment::fromStorage('public/'.$media->path);
        }
        
        return $attachments;
    }

    /**
     * Triggered when the queued mail is failed.
     * 
     * @param  \Throwable  $exception
     * 
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        // ...
    }

}
```

Then we need to costumize the `resources/views/mails/need-approval-mail.blade.php`.

```html
<x-mails.layouts.app>
    <x-slot:title>Approval Issue {{ $issue->issue_number }}</x-slot:title>
    <x-slot:subtitle>This issue need to bee approve immediately</x-slot:subtitle>

    <p>
        Issue Number : {{ $issue->issue_number }} <br>
        Issue Title : {{ $issue->issue_title }} <br>
        Issue Description : {{ $issue->issue_description }} <br>
    </p>

    <x-slot:button url="{{ route('issue.detail', $issue->issue_uuid) }}">
        View Issue
    </x-slot:button>
</x-mails.layouts.app>
```

Then on your controller or action.

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\NeedApprovalMail;

// If you direct send

Mail::to(['avp@pupukkaltim.com'])
    ->cc(['vp@pupukkaltim.com'])
    ->bcc(['superadmin@pupukkaltim.com'])
    ->send(new NeedApprovalMail($issue));

// If you use queue

Mail::to(['avp@pupukkaltim.com'])
    ->cc(['vp@pupukkaltim.com'])
    ->bcc(['superadmin@pupukkaltim.com'])
    ->queue(new NeedApprovalMail($issue));
```

**Result**

<img src="/art/MailDesktop.png" alt="Mail">

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

### Additional Route Function

To minimalize the middleware route for authentication and authorize user request by roles, there are 2 additional function you can use if needed `->authenticated()` and `->roles(['role'])`.

`authenticated` simplify the middleware that rather the authentication method using default, ldap, or sso. So the route will look cleaner and easier to understand. `roles` used to define the authorization of the route by user role.

**Example**
```php
# routes/web.php

Route::get('/test', TestController::class)->authenticated()->roles(["user", 'viewer']);

Route::authenticated()->group(function() {
    Route::get('/issues', [IssueController::class, 'index'])->can('issue.manage')->roles(["Employee"]);
});
```

### Has Created and Updated By

It's recommended for you to implement created and updated by to audit your data history on your database table. So we provide trait to easily add `created_by` and `updated_by` column on your database table.

Example on your migration, you need to add `$table->createdUpdatedBy();`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tr_transactions', function (Blueprint $table) {
            ...
            $table->createdUpdatedBy();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_transactions', function (Blueprint $table) {
            ...
            $table->dropCreatedUpdatedBy();
        });
    }
};
```

Example on your model, you need to add `HasCreatedUpdatedBy`
```php
<?php

use Pkt\StarterKit\Traits\HasCreatedUpdatedBy;
...

class Transaction extends Model
{
    use HasCreatedUpdatedBy;
    ...
}
```

If you implemented `HasCreatedUpdatedBy` you can access `createdBy()` and `updatedBy()` relation from your model.
```php
$transaction = Transaction::query()->with(['createdBy', 'updatedBy'])->first();

$transaction->createdBy;
$transaction->updatedBy;
```

### Custom Encrypt Helper

This helper is to encrypt and decrypt data. By default, the encrypting method using `AES-256-CBC` algorithm. You can also configure your own crypt method, key, option, and iv from `.env` file. The key that you can use to setup the key, iv, and method are `CRYPT_CHIPER`, `CRYPT_KEY`, and `CRYPT_IV`.

```php
use Pkt\StarterKit\Helpers\Crypt;

/**
 * Encrypt data
 * 
 * @param string|integer $string (required)
 * @param ?string $method (optional)
 * @param ?string $iv (optional)
 * @param ?string $key (optional)
 * 
 * @return string
 */
$encrypt = Crypt::encrypt(string|integer $string, string $method = null, string $iv = null, string $key = null);

/**
 * Decrypt the given string.
 *
 * @param string|integer $string (required)
 * @param ?string $method (optional)
 * @param ?string $iv (optional)
 * @param ?string $key (optional)
 *
 * @return string
 */
$decrypt = Crypt::decrypt(string|integer $string, string $method = null, string $iv = null, string $key = null);


/**
 * Check if the encrypted value is valid
 * 
 * @param string $encrypted
 * 
 * @return bool
 */
$isValid = Crypt::isValid(string $encrypted);


/**
 * Check if the encrypted value is invalid
 * 
 * @param string $encrypted
 * 
 * @return bool
 */
$isInvalid = Crypt::isInvalid(string $encrypted);

/**
 * Check if the given value is same as the encrypted value.
 *
 * @param string $value
 * @param string $encryptedValue
 * 
 * @return bool
 */
$check = Crypt::check($value, $encryptedValue);
```

**Example**

```php
use Pkt\StarterKit\Helpers\Crypt;

$encrypt = Crypt::encrypt('data');
$decrypt = Crypt::decrypt('l6123hgs/o0adlRkCtf/36+dW19dTkQ==', iv:'custom_iv_123456', key:'custom_key');
```

### Encrypting Data To Database Table

When you wanna store the **personal** or **restricted** informations on your database, it's **IMPORTANT** to encrypt its data before you store in to the database.

So there's additional helper for you to easily protect the restricted data on the database and querying its data.  By default, the encrypting method using `AES-256-CBC` algorithm. You can also configure your own crypt method, key, option, and iv from `.env` file. The key that you can use to setup the key, iv, and method are `CRYPT_CHIPER`, `CRYPT_KEY`, and `CRYPT_IV`.


**Example**

If you wanna store the personal number such as NIK on your `ms_employees` table.

On your `migration`, it's **recommended** to make the type of column into `encrypted` or `text` because the encrypted data is longer than the original data.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ms_employees', function (Blueprint $table) {
            ...
            $table->encrypted('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_employees');
    }
};
```


On your `model`, you need to cast `nik` column into `Encrypted::class`. By default, return decrypted value will be `string`, but you can also define the actual type of each encrypted column with:
- `Encrypted::class.':int'`
- `Encrypted::class.':integer'`
- `Encrypted::class.':float'`
- `Encrypted::class.':bool'`
- `Encrypted::class.':boolean'`
- `Encrypted::class.':array'`
- `Encrypted::class.':object'`
- `Encrypted::class.':collection'`

```php
<?php

use Pkt\StarterKit\Casts\Encrypted;
...

class Employee extends Model
{
    ...
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nik' => Encrypted::class,
    ];
}
```

If you already set the cast on your spesific column, it will automatically encrypting your data when store and decrypting when its called. So you can insert or update the data using a normal way without worrying about the encryption. 

```php
<?php

Employee::create([
    ...
    'nik' => 1234567890123456
]);

# or

Employee::find(1)
    ->update([
        ...
        'nik' => 0123456789012345
    ]);

```

Additional query builder you can use.
1. `whereEncrypted(column, operator, value)`
```php
$employees = Employee::query()
       ->whereEncrypted('nik', 1234567890123456)
       ->get();
```

2. `orWhereEncrypted(column, operator, value)`
```php
$employees = Employee::query()
       ->whereEncrypted('nik', 1234567890123456)
       ->orWhereEncrypted('nik', '!=', 0123456789012345)
       ->get();
```
3. `whereEncryptedIn(column, [value])`
```php
$employees = Employee::query()
       ->whereEncryptedIn('nik', [1234567890123456, 0123456789012345])
       ->get();
```
4. `orWhereEncryptedIn(column, [value])`
```php
$employees = Employee::query()
       ->whereEncryptedIn('nik', [1234567890123456, 0123456789012345])
       ->orWhereEncryptedIn('nik', [1234567890123457])
       ->get();
```
5. `whereEncryptedNotIn(column, [value])`
```php
$employees = Employee::query()
       ->whereEncryptedNotIn('nik', [1234567890123456, 0123456789012345])
       ->get();
```
6. `orWhereEncryptedNotIn(column, [value])`
```php
$employees = Employee::query()
       ->whereEncryptedNotIn('nik', [1234567890123456, 0123456789012345])
       ->orWhereEncryptedNotIn('nik', [1234567890123457])
       ->get();
```
7. `whereEncryptedRelation(relation, column, operator, value)`
```php
$employees = Employee::query()
       ->whereEncryptedRelation('user', 'userame', 'John Doe')
       ->get();
```
8. `search(columns, value)`
```php
$employees = Employee::query()
       ->search(['name', 'username'] 'John Doe')
       ->get();

// You can also search column from BelongsTo to relation using (.) delimiter
$employees = Employee::query()
       ->search([
            'user.npk',
            'user.username',
            'nik'
       ], 'K236615')
       ->get();
```

<!-- ### Re-encrypting Encrypted Data In Database Table

**Carefully this command can make your data broken if you not setup correctly!**

First, you need to add `CRYPT_REGENERATE` to `.env` file with value `true`.
```.env
...
CRYPT_REGENERATE=true
...
```

Sometimes for better secure, you need to always change the encryption `key` and `iv`, but the problem when you change the encryption key is the data will can't be decrypted because the key was change, so you can use this command to re-encrypt the encrypted data that casting with `Encrypted::class` cast.

```cmd
php artisan pkt:regenerate-encrypted-data
```
Before runing this command, make sure you already add `CRYPT_PREVIOUS_KEY` and `CRYPT_PREVIOUS_IV` to your `.env` file with the value is the existing `CRYPT_KEY` and `CRYPT_IV`. After that replace the `CRYPT_KEY` and `CRYPT_IV` with your new key and iv. To verify the value.

```cmd
php artisan pkt:regenerate-encrypted-data --generate
```
Before runing this command, make sure you didn't change the `CRYPT_KEY` and `CRYPT_IV`. When you add the `--generate` flag it will automatic generate the new `CRYPT_KEY` and `CRYPT_IV` and make the existing key and iv to `CRYPT_PREVIOUS_KEY` and `CRYPT_PREVIOUS_IV` on `.env` file. -->

### Media Library

In this package, we provide a helper to easily manage your media file such as image, video, or document. The media can be attach to assigned Model so you need to configure your Model before you can use media functional.

Before using media library, you need to initialize first
```cmd
php artisan pkt:media-init
php artisan migrate
```
This will create
```
app/Http/Controllers/Starter/MediaController.php
app/Models/Media.php
database/migrations/2024_06_11_000000_create_media_table.php
database/migrations/2024_06_11_000000_create_mediables_table.php
```

#### How to use

If you already initialize media library, you can add `InteractsWithMedia` traits to your model that need to interact with media. This will indicate that your model will have a relationship with `Media` table or model. By default the relation with media will be `ManyToMany` so you can attach to existing media if needed.

The default visibility of uploaded file on `storage` can be define from `app/Models/Media` and change the constant value of `DEFAULT_VISIBILITY` to `public` or `private`. When you set the visibility to **public**, it will store your file to `storage/app/public` it's mean that when you run the `php artisan storage:link` command it will symlink all files and folders on public storage to public that can be access by other directly. Also if you set the visibility to **private** it will store your file to `storage/app/private` that only can be access from your action.

```php
...
const DEFAULT_VISIBILITY = 'public';
...

# Or

...
const DEFAULT_VISIBILITY = 'private';
...
```

**Available Method**

When it's having `Many to Many` relationship between your Model and Media model, you can use all ManyToMany method. You also can using dynamic relationship to media using `with` and for the params `mediaCollectionName` where CollectionName is based on existing collections.
```php
$issue = Issue::query()->with('media')->get();
$issue = Issue::query()->with(['mediaEvidences', 'mediaReport'])->get();

$issue->media()->get();
$issue->media()->first();

$name = $issues->mediaEvidences()->first()->original_name;
$file = $issues->mediaEvidences()->first()->url;

... (etc)
```

There's a additional helper you can use to make uploaded format from base64 format.

```php
use Pkt\StarterKit\Helpers\FileHelper;

/**
 * @param string $base64
 * @param string|null $filename
 * 
 * @return UploadedFile
 */
$file = FileHelper::fromBase64(string $base64, ?string $filename = null);
```

Available additional method you can use to interact with media from your model.
1. `setMediaCollection($collectionName)`, 
this will set media collection you want to interact.
```php
$issue = Issue::create($validated);
$issue
    ->setMediaCollection('evidences')
    ->attachMediaFromElementRequest($validated['evidences']);
```

2. `setMediaVisibility($visibility)`, 
this will override the default media visibility on your model. The media visibility that only can be accepted are `public` or `private`.
```php
$issue = Issue::create($validated);
$issue
    ->setMediaVisibility('private')
    ->attachMediaFromElementRequest($validated['evidences']);
```

3.  `attachMediaFromElementRequest($media, $collectionName, $visibility)`, this will store your file to `storage/app/public/[collectionName]` and automatically attach the file to your data from [Elemen Plus](https://element-plus.org/en-US/component/upload.html#file-list-control) upload file request.
```php
/**
 * ================================================
 * Element request should look like this:
 * ================================================
 * 
 * array:1 [
 * 0 => array:6 [
 *   "name" => "filename.pdf"
 *   "percentage" => "0"
 *   "status" => "ready"
 *   "size" => "45018"
 *   "uid" => "1718114776003"
 *   "raw" => UploadedFile {
 *       ...
 *   }
 * ],
 *]
 */

$issue = Issue::create($validated);
$issue->attachMediaFromElementRequest($validated['evidences'], 'evidences');
```

4.  `attachMediaFromExisting($media, $collectionName)`, this will attach an existing media to your data.
```php
$issue = Issue::create($validated);
$media = Media::query()->where('id', 1)->first()
$issue->attachMediaFromExisting($media, 'evidences');
```

5. `attachMediaFromUploadedFile($media, $collectionName, $visibility)`, this will store your file to `storage/app/public/[collectionName]` and automatically attach the file to your data from uploaded file format.
```php
$issue = Issue::create($validated);
$file = $request->file('report');
$issue->attachMediaFromUploadedFile($file, 'report');
```

6. `attachMediaFromBase64($base64, $collectionName, $visibility)`, this will store your file to `storage/app/public/[collectionName]` and automatically attach the file to your data from base64 file format.
```php
$issue = Issue::create($validated);
$base64 = $validated['base64_sign'];
$issue->attachMediaFromBase64($base64, 'sign');
```

7. `detachMedia($media)`, this will detaching media from your data.
```php
$issue = Issue::find(1);
$issue->detachMedia([1,2,3]);
```

8. `detachMediaFromCollection($collectionName)`, this will detaching all media on specific collection from your data.
```php
$issue = Issue::find(1);
$issue->detachMediaFromCollection('evidences');
```

9. `detachAllMedia()`, this will detaching all media from your data.
```php
$issue = Issue::find(1);
$issue->detachAllMedia();
```

10. `syncMedia($media, $collectionName)`, this will syncing media data on your data.
```php
$issue = Issue::find(1);
$media = Media::query()->whereIn('id', [1,2,3])->get();
$issue->syncMedia($media, 'evidences');
```

11.  `syncMediaFromElementRequest($media, $collectionName, $visibility)`, this will syncing media data on your data from Element Plus request.
```php
/**
 * ================================================
 * Element request should look like this:
 * ================================================
 * 
 * array:2 [
 * 0 => array:17 [
 *   "id" => "82"
 *   "uuid" => "9c569747-0a0c-460d-8242-e9cc1adb3096"
 *   "original_name" => "filename.png"
 *   "type" => "file"
 *   "size" => "33940"
 *   "extension" => null
 *   "mime_type" => "image/png"
 *   "created_at" => "2024-06-21T08:06:31.000000Z"
 *   "updated_at" => "2024-06-21T08:06:31.000000Z"
 *   "deleted_at" => null
 *   "created_by" => "1"
 *   "updated_by" => "1"
 *   "name" => "filename.png"
 *   "url" => "http://127.0.0.1:8000/get-media/9c569747-0a0c-460d-8242-e9cc1adb3096"
 *   "pivot" => array:4 [▶]
 *   "uid" => "1718957545494"
 *   "status" => "success"
 * ],
 * 1 => array:6 [
 *   "name" => "filename.pdf"
 *   "percentage" => "0"
 *   "status" => "ready"
 *   "size" => "45018"
 *   "uid" => "1718114776003"
 *   "raw" => UploadedFile {
 *       ...
 *   }
 * ],
 *]
 */
$validated = $request->validated();
$issue = Issue::find(1);
$issue->syncMediaFromElementRequest($validated['media_evidences'], 'evidences');
```

12.  `syncMediaFromUploadedFile($media, $collectionName, $visibility)`, this will syncing media data from uploaded file format.
```php
$issue = Issue::find(1);
$media = $request->file('media_report')
$issue->syncMediaFromUploadedFile($media, 'report');
```

13.  `syncMediaFromBase64($base64, $collectionName, $visibility)`, this will syncing media data from base64 file format.
```php
$issue = Issue::find(1);
$base64 = $validated['base64_sign']
$issue->syncMediaFromBase64($base64, 'sign');
```

14.   `getAllMedia()`, this will get all media related from data.
```php
$issue = Issue::find(1);
$issue->getAllMedia();
```

15.   `getMediaFromCollection($collectionName)`, this will get all media from specific collection related from data.
```php
$issue = Issue::find(1);
$issue->getMediaFromCollection('evidences');
```

16.   `getFirstMediaFromCollection($collectionName)`, this will get first media from specific collection related from data.
```php
$issue = Issue::find(1);
$issue->getFirstMediaFromCollection('report');
```

17.   `getAcceptedMediaCollections()`, this will display accepted media that can be assign from model.
```php
$issue = Issue::getAcceptedMediaCollections();
```

18.   `getAvailableMediaCollections()`, this will display available media collection on your model.
```php
$issue = Issue::getAvailableMediaCollections();
```

**Other Method From Media Model**
1. `getAllCollections()`, this will display all existing collection on your media.
```php
$media = Media::getAllCollections();
```

2. `createFromElementRequest($media, $collectionName, $visibility)`, this will store your file from [Elemen Plus](https://element-plus.org/en-US/component/upload.html#file-list-control) upload file request to `storage/app/public/[collectionName]` and automatically add the media to database.
```php
$elementRequest = $validated['evidences'];
$media = Media::createFromElementRequest($elementRequest, 'evidences');
```

3. `createFromUploadedFile($file, $collectionName, $visibility)`, this will store your file from uploaded file format to `storage/app/public/[collectionName]` and automatically add the media to database.
```php
$file = $request->file('report');
$media = Media::createFromUploadedFile($file, 'report', 'private');
```

4. `createFromBase64($base64, $collectionName, $visibility)`, this will store your file from base64 file format to `storage/app/public/[collectionName]` and automatically add the media to database.
```php
$file = $validated['base64_sign'];
$media = Media::createFromBase64($file, 'sign');
```

**FOR EXAMPLE**

You have `Issue` model that need to interact with `evidence` that can be more than 1 and can be from other issue or existing file, and `report` file that can only 1.

- On your `Issue` model
```php
<?php

use Pkt\StarterKit\Traits\InteractsWithMedia;
...

class Issue extends Authenticatable
{
    use InteractsWithMedia;
    ...

    /**
     * The collection that accepted on your model
     *
     * @var array<int, string>
     */
    protected $acceptedMediaCollections = [
        'evidences', 
        'report'
    ];

    ...
}
```
You can use `InteractsWithMedia` traits and **optional** but **recommended** you can add `protected $acceptedMediaCollections` to predefined collections that allowed on your Model, by default you can use any collection name.

- On your create controller
```php
<?php

...
public function create(CreateIssueRequest $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validated();
        $media = Media::query()->whereIn('id', $validated['related_evidences'])->get();
        $issue = Issue::query()->create($validated);

        $issue->attachMediaFromElementRequest($validated['media_evidences'], 'evidences');
        $issue->attachMediaFromExisting($media, 'evidences');
        $issue->attachMediaFromElementRequest($validated['media_report'], 'report', 'private');
    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()->back()->withErrors([
            'message' => 'Failed to create issue'
        ]);
    }
    DB::commit();
    return redirect()->back()->with('message', 'Success to create issue');
}
...
```

- On your update controller
```php
<?php

...
public function update(Issue $issue, UpdateIssueRequest $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validated();
        $issue->update($validated);
        $issue->syncMediaFromElementRequest($request['media_evidences'], 'evidences');
        $issue->syncMediaFromElementRequest($request['media_report'], 'report', 'private');
    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()->back()->withErrors([
            'message' => 'Failed to update issue'
        ]);
    }
    DB::commit();
    return redirect()->back()->with('message', 'Success to update issue');
}
...
```

Additional, you can register the scheduller to remove unused media to prevent the files from becoming trash in the storage. On your `app/Console/Kernel.php` add to schedule method.

```php
use Pkt\StarterKit\Actions\DeleteUnusedMedia;

/**
 * Define the application's command schedule.
 */
protected function schedule(Schedule $schedule): void
{
    ...

    $schedule->call(new DeleteUnusedMedia)
        ->monthlyOn(1, '00:00');
    
    ...
}
```
