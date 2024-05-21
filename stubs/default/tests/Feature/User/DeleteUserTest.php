<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->permission = Permission::create(['name' => 'user.browse']);
    $this->role = Role::create(['name' => 'Test']);
    $this->role->givePermissionTo($this->permission);

    $this->user = User::factory()->create();
    $this->user->assignRole($this->role);
});

test('does not have access to delete user', function () {
    $this->actingAs($this->user);
    $newUser = User::factory()->create();
    $response = test()->delete(route('user.delete', $newUser->user_uuid));
    expect($response->status())->toBe(403);
});

test('can not delete user not found', function () {
    $permission = Permission::create(['name' => 'user.delete']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $response = test()->delete(route('user.delete', 'not-found'));
    expect($response->status())->toBe(404);
});

test('can delete user', function () {
    $permission = Permission::create(['name' => 'user.delete']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $oldUser = User::factory()->create();
    $response = test()->delete(route('user.delete', $oldUser->user_uuid));
    expect($response->status())->toBe(302);
    expect(User::where('user_uuid', $oldUser->user_uuid)->exists())->toBeFalse();
});
