<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->permission = Permission::create(['name' => 'user.browse']);
    $this->role = Role::create(['name' => 'Test']);
    $this->role->givePermissionTo($this->permission);

    $this->user = User::factory()->create();
    $this->user->assignRole($this->role);
});

test('user manage without login', function () {
    $response = test()->get(route('user.browse'));
    expect($response->status())->toBe(302);
    expect($response->baseResponse->getTargetUrl())->toBe(route('login'));
});

test('user manage displayed', function () {
    $this->actingAs($this->user);
    $response = test()->get(route('user.browse'));

    expect($response->status())->toBe(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('User/UserManage')
        ->has('roles')
        ->has('leader_enabled')
    );
});
