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
use Pkt\StarterKit\Helpers\LeaderApi;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function userManagePage(Request $request)
    {
        return Inertia::render('User/UserManage', [
            'roles' => Role::all(),
            'leader_enabled' => config('leader.LEADER_API_KEY') != null,
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
            return redirect()->back()->with('message', 'Success to create user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message' => 'Failed to create user'
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
            return redirect()->back()->with('message', 'Success to update user');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'message' => 'Failed to update user'
            ]);
        }
    }
    public function delete(User $user, Request $request)
    {
        $user->delete();
        return redirect()->back()->with('message', 'Success to delete user');
    }
    public function switchStatus(User $user, Request $request)
    {
        $user->update([
            'is_active' => $request['is_active'] ? 1 : 0,
        ]);
        return redirect()->back()->with('message', 'Success to switch user status');
    }
    public function syncLeader(Request $request)
    {
        DB::beginTransaction();
        try {
            $employees = LeaderApi::getAllEmployee();
            $employees->each(function ($employee) {
                User::updateOrCreate([
                    'npk' => $employee->USERS_NPK
                ], [
                    'name' => $employee->USERS_NAME,
                    'email' => $employee->USERS_EMAIL,
                    'username' => $employee->USERS_USERNAME,
                    'hierarchy_code' => $employee->USERS_HIERARCHY_CODE,
                    'position_id' => $employee->USERS_ID_POSISI,
                    'position' => $employee->USERS_POSISI,
                    'work_unit_id' => $employee->USERS_ID_UNIT_KERJA,
                    'work_unit' => $employee->USERS_UNIT_KERJA,
                    'users_flag' => $employee->USERS_FLAG,
                ]);
            });
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Failed to sync leader']);
        }
        DB::commit();
        return redirect()->back()->with('message', 'Success sync leader');
    }
}
