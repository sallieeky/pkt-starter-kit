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

test('does not have access to create modelname', function () {
    $this->actingAs($this->user);
    $response = test()->post(route('model_name.browse'));
    expect($response->status())->toBe(403);
});

test('can create modelname', function () {
    $permission = Permission::create(['name' => 'model_name.create']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $newData = ModelName::factory()->create()->toArray();
    $response = test()->post(route('model_name.create'), $newData);

    expect($response->status())->toBe(302);
    expect(ModelName::where('primary_key', $newData['primary_key'])->exists())->toBeTrue();
});
