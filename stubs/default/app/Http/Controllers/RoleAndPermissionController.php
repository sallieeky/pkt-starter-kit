<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Requests\Role\UpdateRolePermissionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionController extends Controller
{
    public function roleAndPemissionManagePage(Request $request)
    {
        return Inertia::render('User/RoleAndPermissionManage', [
            'roles' => Role::all(),
        ]);
    }
    
    public function create(RoleRequest $request)
    {
        $validated = $request->validated();
        Role::create($validated);
        return redirect()->back()->with('message','Success to create role');
    }

    public function update(Role $role, RoleRequest $request)
    {
        $validated = $request->validated();
        if($role->id == 1){
            return redirect()->back()->withErrors(['message'=>'Superadmin cannot be changed']);
        }
        $role->update($validated);
        return redirect()->back()->with('message','Success to update role');
    }
    
    public function delete(Role $role, Request $request)
    {
        if($role->id == 1){
            return redirect()->back()->withErrors(['message'=>'Superadmin cannot be deleted']);
        }
        $role->delete();
        return redirect()->back()->with('message','Success to delete role');
    }

    public function getRolePermission(Role $role, Request $request)
    {
        try{
            $roleId = $role->id;
            $permissions = Permission::select('id', 'name')
                ->leftJoin('role_has_permissions', function ($join) use ($roleId) {
                    $join->on('permissions.id', '=', 'role_has_permissions.permission_id')
                        ->where('role_has_permissions.role_id', '=', $roleId);
                })
                ->addSelect(DB::raw('
                    CASE 
                        WHEN role_has_permissions.role_id IS NOT NULL
                        THEN 1 
                        ELSE 0 
                    END AS role_has_permission'))
                ->orderBy('id')
                ->get();
            $permissions = $permissions->map(function ($p){
                $p->role_has_permission = intval($p->role_has_permission);
                return $p;
            });
            $permissionsGrouped = $permissions->groupBy(function ($item) {
                return explode('.', $item['name'])[0];
            });
            return response()->json([
                'status' => true,
                'message' => 'Success to get permission list',
                'data' => [
                    'permissions' => $permissionsGrouped,
                    'total_assigned_permission' => $permissions->where('role_has_permission', 1)->count(),
                ],
            ],200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get permission list',
                'data' => [],
            ],500);
        }
    }

    public function getRoleUser(Role $role, Request $request)
    {
        try{
            $roleId = $role->id;
            $users = User::with(['roles'])
                    ->whereHas('roles', fn($q)=> $q->where('id', '=', $roleId))
                    ->get();
            return response()->json([
                'status' => true,
                'message' => 'Success to get user list',
                'data' => [
                    'users' => $users,
                    'user_count' => $users->count(),
                ],
            ],200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get user list',
                'data' => [],
            ],500);
        }
    }

    public function switchPermission(Role $role,UpdateRolePermissionRequest $request)
    {
        $validated = $request->validated();
        if($role->id == 1){
            return response()->json([
                'status' => false,
                'message' => 'Superadmin cannot be changed',
            ], 403);
        }
        try {
            $permission = Permission::find($validated['id_permission']);
            if($validated['value']){
                $role->givePermissionTo($permission);
            }else{
                $role->revokePermissionTo($permission);
            }
            return response()->json([
                'status' => true,
                'message' => 'Successfully updated role permissions',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update role permissions',
                'data' => $th,
            ], 500);
        }
    }
}
