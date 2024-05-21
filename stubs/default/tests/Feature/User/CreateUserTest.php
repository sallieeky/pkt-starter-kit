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

test('does not have access to create user', function () {
    $this->actingAs($this->user);
    $response = test()->post(route('user.browse'));
    expect($response->status())->toBe(403);
});

test('can create user', function () {
    $permission = Permission::create(['name' => 'user.create']);
    $this->user->givePermissionTo($permission);

    $this->actingAs($this->user);
    $newUser = [
        'name' => fake()->name(),
        'username' => fake()->unique()->userName(),
        'npk' => fake()->unique()->numerify('##########'),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'role' => '',
    ];
    $response = test()->post(route('user.create'), $newUser);

    expect($response->status())->toBe(302);
    expect(User::where('username', $newUser['username'])->exists())->toBeTrue();
});
