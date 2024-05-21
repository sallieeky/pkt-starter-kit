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

test('does not have access to update user', function () {
    $this->actingAs($this->user);
    $newUser = User::factory()->create();
    $response = test()->put(route('user.update', $newUser->user_uuid));
    expect($response->status())->toBe(403);
});

test('can not update user not found', function () {
    $permission = Permission::create(['name' => 'user.update']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $response = test()->put(route('user.update', 'not-found'), []);
    expect($response->status())->toBe(404);
});

test('can update user', function () {
    $permission = Permission::create(['name' => 'user.update']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $oldUser = User::factory()->create();
    $newUser = [
        'username' => fake()->unique()->userName(),
        'name' => fake()->name(),
        'npk' => fake()->unique()->numerify('##########'),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'new-password',
        'role' => '',
    ];
    $response = test()->put(route('user.update', $oldUser->user_uuid), $newUser);
    expect($response->status())->toBe(302);
    $this->assertDatabaseHas('users', [
        'username' => $newUser['username'],
        'name' => $newUser['name'],
        'npk' => $newUser['npk'],
        'email' => $newUser['email'],
    ]);
});
