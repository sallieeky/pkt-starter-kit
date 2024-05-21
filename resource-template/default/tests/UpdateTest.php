<?php

use App\Models\User;
use App\Models\ModelName;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->permission = Permission::create(['name' => 'model_name.browse']);
    $this->role = Role::create(['name' => 'Test']);
    $this->role->givePermissionTo($this->permission);

    $this->user = User::factory()->create();
    $this->user->assignRole($this->role);
});

test('does not have access to update modelname', function () {
    $this->actingAs($this->user);
    $newData = ModelName::factory()->create();
    $response = test()->put(route('model_name.update', $newData->primary_key));
    expect($response->status())->toBe(403);
});

test('can not update modelname not found', function () {
    $permission = Permission::create(['name' => 'model_name.update']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $response = test()->put(route('model_name.update', 'not-found'), []);
    expect($response->status())->toBe(404);
});

test('can update modelname', function () {
    $permission = Permission::create(['name' => 'model_name.update']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $oldData = ModelName::factory()->create();
    $newData = ModelName::factory()->make()->toArray();
    $response = test()->put(route('model_name.update', $oldData->primary_key), $newData);
    expect($response->status())->toBe(302);
    $this->assertDatabaseHas('table_names', $newData);
});
