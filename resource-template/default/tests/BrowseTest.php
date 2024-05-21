<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->permission = Permission::create(['name' => 'model_name.browse']);
    $this->role = Role::create(['name' => 'Test']);
    $this->role->givePermissionTo($this->permission);

    $this->user = User::factory()->create();
    $this->user->assignRole($this->role);
});

test('not login to access modelname manage', function () {
    $response = test()->get(route('model_name.browse'));
    expect($response->status())->toBe(302);
    expect($response->baseResponse->getTargetUrl())->toBe(route('login'));
});

test('can display modelname manage', function () {
    $this->actingAs($this->user);
    $response = test()->get(route('model_name.browse'));

    expect($response->status())->toBe(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('ModelName/ModelNameManage')
    );
});
