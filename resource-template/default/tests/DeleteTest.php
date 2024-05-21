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

test('does not have access to delete modelname', function () {
    $this->actingAs($this->user);
    $newData = ModelName::factory()->create();
    $response = test()->delete(route('model_name.delete', $newData->primary_key));
    expect($response->status())->toBe(403);
});

test('can not delete modelname not found', function () {
    $permission = Permission::create(['name' => 'model_name.delete']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $response = test()->delete(route('model_name.delete', 'not-found'));
    expect($response->status())->toBe(404);
});

test('can delete modelname', function () {
    $permission = Permission::create(['name' => 'model_name.delete']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $oldData = ModelName::factory()->create();
    $response = test()->delete(route('model_name.delete', $oldData->primary_key));
    expect($response->status())->toBe(302);
    expect(ModelName::where('primary_key', $oldData->primary_key)->exists())->toBeFalse();
});
