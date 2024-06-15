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

### 1. Make resource command
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
        Schema::dropViewIfExists('table_name');

        /**
         * ============================================
         * Create the view with the given query.
         * ============================================
         */
        $query = \App\Models\User::query()
            ->where('is_active', true);
        Schema::createView('vw_active_users', $query);
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

This helper is to encrypt and decrypt data. By default, the encrypting method using `AES-256-CBC` algorithm and it's associated with `APP_KEY`, so it's **IMPORTANT** to keep your `APP_KEY` **secure**. Or if you have encrypted data with different key, you can replace the key from params.

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

```

**Example**

```php
use Pkt\StarterKit\Helpers\Crypt;

$encrypt = Crypt::encrypt('data');
$decrypt = Crypt::decrypt('l6123hgs/o0adlRkCtf/36+dW19dTkQ==', iv:'custom_iv_123456', key:'custom_key');
```

### Encrypting Data To Database Table

When you wanna store the **personal** or **restricted** informations on your database, it's **IMPORTANT** to encrypt its data before you store in to the database.

So there's additional helper for you to easily protect the restricted data on the database and querying its data. The encrypting method associated with `APP_KEY`, so it's **IMPORTANT** to keep your `APP_KEY` **secure**.


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

### Media Library

In this package, we provide a helper to easily manage your media file such as image, video, or document. The media can be attach to assigned Model so you need to configure your Model before you can use media functional.

Before using media library, you need to initialize first
```cmd
php artisan pkt:media-init
php artisan migrate
```
This will create
```
app/Http/Controllers/MediaController.php
app/Models/Media.php
database/migrations/2024_06_11_000000_create_media_table.php
database/migrations/2024_06_11_000000_create_mediables_table.php
```
Additional **RECOMMENDED** action, you can add this code to `routes/starter.php` or `routes/web.php`
```php
use App\Http\Controllers\MediaController;

Route::get('/get-media/{media:uuid}', [MediaController::class, 'getMedia'])->name('get-media');
```

#### How to use

If you already initialize media library, you can add `InteractsWithMedia` traits to your model that need to interact with media. This will indicate that your model will have a relationship with `Media` table or model. By default the relation with media will be `ManyToMany` so you can attach to existing media if needed.

**Available Method**

When it's having `Many to Many` relationship between your Model and Media model, you can use all ManyToMany method. You also can using dynamic relationship to media using `with` and for the params `mediaCollectionName` where CollectionName is based on existing collections.
```php
$issue = Issue::query()->with('media')->get();
$issue = Issue::query()->with(['mediaEvidences', 'mediaReport'])->get();

$issue->media()->get();
$issue->media()->first();

... (etc)
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

2.  `attachMediaFromElementRequest($media, $collectionName)`, this will store your file to `storage/app/public/[collectionName]` and automatically attach the file to your data from [Elemen Plus](https://element-plus.org/en-US/component/upload.html#file-list-control) upload file request.
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

3.  `attachMediaFromExisting($media, $collectionName)`, this will attach an existing media to your data.
```php
$issue = Issue::create($validated);
$media = Media::query()->where('id', 1)->first()
$issue->attachMediaFromExisting($media, 'evidences');
```

4. `attachMediaFromUploadedFile($media, $collectionName)`, this will store your file to `storage/app/public/[collectionName]` and automatically attach the file to your data from uploaded file format.
```php
$issue = Issue::create($validated);
$file = $request->file('report');
$issue->attachMediaFromUploadedFile($file, 'report');
```

5. `detachMedia($media)`, this will detaching media from your data.
```php
$issue = Issue::find(1);
$issue->detachMedia([1,2,3]);
```

6. `detachMediaFromCollection($collectionName)`, this will detaching all media on specific collection from your data.
```php
$issue = Issue::find(1);
$issue->detachMediaFromCollection('evidences');
```

7. `detachAllMedia()`, this will detaching all media from your data.
```php
$issue = Issue::find(1);
$issue->detachAllMedia();
```

8. `syncMedia($media, $collectionName)`, this will syncing media data on your data.
```php
$issue = Issue::find(1);
$media = Media::query()->whereIn('id', [1,2,3])->get();
$issue->syncMedia($media, 'evidences');
```

9. `getAllMedia()`, this will get all media related from data.
```php
$issue = Issue::find(1);
$issue->getAllMedia();
```

10. `getMediaFromCollection($collectionName)`, this will get all media from specific collection related from data.
```php
$issue = Issue::find(1);
$issue->getMediaFromCollection('evidences');
```

11. `getFirstMediaFromCollection($collectionName)`, this will get first media from specific collection related from data.
```php
$issue = Issue::find(1);
$issue->getFirstMediaFromCollection('report');
```

12. `getAcceptedMediaCollections()`, this will display accepted media that can be assign from model.
```php
$issue = Issue::getAcceptedMediaCollections();
```

13. `getAvailableMediaCollections()`, this will display available media collection on your model.
```php
$issue = Issue::getAvailableMediaCollections();
```

**Other Method From Media Model**
1. `getAllCollections()`, this will display all existing collection on your media.
```php
$media = Media::getAllCollections();
```

2. `createFromElementRequest($media, $collectionName)`, this will store your file from [Elemen Plus](https://element-plus.org/en-US/component/upload.html#file-list-control) upload file request to `storage/app/public/[collectionName]` and automatically add the media to database.
```php
$elementRequest = $validated['evidences'];
$media = Media::createFromElementRequest($elementRequest, 'evidences');
```

3. `createFromUploadedFile($file, $collectionName)`, this will store your file from uploaded file format to `storage/app/public/[collectionName]` and automatically add the media to database.
```php
$file = $request->file('report');
$media = Media::createFromUploadedFile($file, 'report');
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
        $media = Media::query()->whereIn('id', $validated['relatedEvidence'])->get();
        $issue = Issue::create($validated);
        
        $issue->attachMediaFromElementRequest($validated['evidence'], 'evidences');
        $issue->attachMediaFromExisting($media, 'evidences');
        $issue->attachMediaFromElementRequest($validated['report'], 'report');
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
