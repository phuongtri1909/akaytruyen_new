<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:xem_danh_sach_quyen')->only('index');
        $this->middleware('can:gan_quyen_cho_vai_tro')->only('getRoles', 'assignRoles');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->role . '%');
            });
        }

        $permissions = $query->paginate(15);
        $roles = Role::all();
        
        
        $totalPermissions = Permission::count();
        $assignedPermissions = Permission::whereHas('roles')->count();
        $unassignedPermissions = $totalPermissions - $assignedPermissions;

        return view('Admin.pages.permissions.index', compact('permissions', 'roles', 'totalPermissions', 'assignedPermissions', 'unassignedPermissions'));
    }

    /**
     * Lấy vai trò cho gán quyền
     */
    public function getRoles(Request $request)
    {
        $permissionId = $request->get('permission_id');
        $permission = Permission::findOrFail($permissionId);
        
        $roles = Role::all();
        $assignedRoles = $permission->roles->pluck('id')->toArray();

        return response()->json([
            'roles' => $roles,
            'assignedRoles' => $assignedRoles
        ]);
    }

    /**
     * Gán vai trò cho quyền
     */
    public function assignRoles(Request $request)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $permission = Permission::findOrFail($request->permission_id);
        
        
        $roleNames = Role::whereIn('id', $request->roles ?? [])->pluck('name')->toArray();
        $permission->syncRoles($roleNames);

        return response()->json([
            'success' => true,
            'message' => 'Gán vai trò thành công!'
        ]);
    }
}
