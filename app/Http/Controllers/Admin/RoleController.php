<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_vai_tro,them_vai_tro,sua_vai_tro,xoa_vai_tro')->only('index');
        $this->middleware('can:them_vai_tro')->only('create', 'store');
        $this->middleware('can:sua_vai_tro')->only('edit', 'update');
        $this->middleware('can:xoa_vai_tro')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('permission')) {
            $query->whereHas('permissions', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->permission . '%');
            });
        }

        $roles = $query->paginate(15);
        $permissions = Permission::all();

        return view('Admin.pages.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('Admin.pages.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255|unique:roles,name',
        ];
        
        $validationMessages = [
            'name.required' => 'Tên vai trò là bắt buộc',
            'name.string' => 'Tên vai trò phải là chuỗi',
            'name.max' => 'Tên vai trò không được vượt quá 255 ký tự',
            'name.unique' => 'Tên vai trò đã tồn tại',
        ];

        // Chỉ validate permissions nếu user có quyền gán quyền
        if (auth()->user()->can('gan_quyen_cho_vai_tro')) {
            $validationRules['permissions'] = 'required|array|min:1';
            $validationRules['permissions.*'] = 'exists:permissions,id';
            $validationMessages['permissions.required'] = 'Quyền là bắt buộc';
        }

        $request->validate($validationRules, $validationMessages);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        // Chỉ gán quyền nếu user có quyền gán quyền
        if (auth()->user()->can('gan_quyen_cho_vai_tro') && $request->has('permissions')) {
            $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vai trò đã được tạo thành công!',
                'redirect' => route('admin.roles.index')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Vai trò đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'Admin') {
            abort(403, 'Không thể chỉnh sửa vai trò Admin');
        }

        $permissions = Permission::all();
        return view('Admin.pages.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Admin') {
            abort(403, 'Không thể chỉnh sửa vai trò Admin');
        }

        $validationRules = [];
        $validationMessages = [];

        // Chỉ validate permissions nếu user có quyền gán quyền
        if (auth()->user()->can('gan_quyen_cho_vai_tro')) {
            $validationRules['permissions'] = 'required|array|min:1';
            $validationRules['permissions.*'] = 'exists:permissions,id';
            $validationMessages['permissions.required'] = 'Quyền là bắt buộc';
        }

        if (!$role->protected) {
            $validationRules['name'] = 'required|string|max:255|unique:roles,name,' . $role->id;
            $validationMessages = array_merge($validationMessages, [
                'name.required' => 'Tên vai trò là bắt buộc',
                'name.string' => 'Tên vai trò phải là chuỗi',
                'name.max' => 'Tên vai trò không được vượt quá 255 ký tự',
                'name.unique' => 'Tên vai trò đã tồn tại',
            ]);
        }

        $request->validate($validationRules, $validationMessages);

        if (!$role->protected) {
            $role->update([
                'name' => $request->name
            ]);
        }

        // Chỉ gán quyền nếu user có quyền gán quyền
        if (auth()->user()->can('gan_quyen_cho_vai_tro') && $request->has('permissions')) {
            $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vai trò đã được cập nhật thành công!',
                'redirect' => route('admin.roles.index')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Vai trò đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->protected) {
            abort(403, 'Không thể xóa vai trò được bảo vệ');
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vai trò đã được xóa thành công!'
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Vai trò đã được xóa thành công!');
    }
}
