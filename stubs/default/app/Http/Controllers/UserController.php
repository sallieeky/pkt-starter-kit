<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\DxAdapter;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function userManagePage(Request $request)
    {
        return Inertia::render('User/UserManage', [
            'roles' => Role::all(),
        ]);
    }
    public function dataProcessing(Request $request)
    {
        $loadData = User::with(['roles'])->select('*');
        $loadDatais = DxAdapter::load($loadData);
        $data = $loadDatais->paginate($request->take ?? $loadDatais->count());
        return response()->json([
            'status' => true,
            'data' => $data->items(),
            'totalCount' => $data->total(),
        ], 200);
    }
    public function create(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);
            $user->syncRoles($validated['role']);

            DB::commit();
            return redirect()->back()->with('message','Success to create user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message'=>'Failed to create user'
            ]);
        }
    }
    public function update(User $user, UpdateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user->update($validated);
            $user->syncRoles($validated['role']);

            DB::commit();
            return redirect()->back()->with('message','Success to update user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message'=>'Failed to update user'
            ]);
        }
    }
    public function delete(User $user, Request $request)
    {
        $user->delete();
        return redirect()->back()->with('message','Success to delete user');
    }
    public function switchStatus(User $user, Request $request)
    {
        $user->update([
            'is_active' => $request['is_active'] ? 1 : 0,
        ]);
        return redirect()->back()->with('message','Success to switch user status');
    }
}
