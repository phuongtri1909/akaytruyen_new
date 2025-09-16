<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserBan;
use App\Models\BanIp;
use App\Models\SMTPSetting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_nguoi_dung,them_nguoi_dung,sua_nguoi_dung,xoa_nguoi_dung')->only('index');
        $this->middleware('can:them_nguoi_dung')->only('create', 'store');
        $this->middleware('can:sua_nguoi_dung')->only('edit', 'update', 'ban', 'getBanInfo', 'banIp', 'unbanIp');
        $this->middleware('can:xoa_nguoi_dung')->only('destroy');
    }

    /**
     * Kiểm tra xem user hiện tại có phải Super Admin không
     */
    private function isSuperAdmin()
    {
        $smtpSetting = SMTPSetting::first();
        return $smtpSetting && $smtpSetting->admin_email === auth()->user()->email;
    }

    /**
     * Kiểm tra xem user có phải Super Admin không
     */
    private function isUserSuperAdmin(User $user)
    {
        $smtpSetting = SMTPSetting::first();
        return $smtpSetting && $smtpSetting->admin_email === $user->email;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = User::with(['roles', 'userBan', 'banIp']);

        
        if ($request->filled('user_id')) {
            $query->where('id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('active', '=', 'active');
            } elseif ($request->status == 'inactive') {
                $query->where('active', '=', 'inactive');
            }
        }

        
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('Admin.pages.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('Admin.pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ], [
            'name.required' => 'Tên người dùng là bắt buộc',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vai trò là bắt buộc',
            'role.exists' => 'Vai trò không tồn tại',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $this->assignRoleWithExpiry($user->id, $request->role);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được tạo thành công!',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        $user->load('roles');
        return view('Admin.pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        if ($this->isUserSuperAdmin($user) && !$this->isSuperAdmin()) {
            abort(403, 'Bạn không có quyền chỉnh sửa Super Admin này!');
        }

        $roles = Role::all();
        $user->load('roles');
        return view('Admin.pages.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        if ($this->isUserSuperAdmin($user) && !$this->isSuperAdmin()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền chỉnh sửa Super Admin này!'
                ], 403);
            }
            abort(403, 'Bạn không có quyền chỉnh sửa Super Admin này!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ], [
            'name.required' => 'Tên người dùng là bắt buộc',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vai trò là bắt buộc',
            'role.exists' => 'Vai trò không tồn tại',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        
        // Gán role với expires_at cho VIP
        $this->assignRoleWithExpiry($user->id, $request->role);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được cập nhật thành công!',
                'redirect' => route('admin.users.index')
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        if ($this->isUserSuperAdmin($user)) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa Super Admin!'
                ], 403);
            }
            abort(403, 'Không thể xóa Super Admin!');
        }

        // Admin thường không thể xóa admin khác nếu không có quyền
        if ($user->hasRole('Admin') && !$this->isSuperAdmin()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa admin khác!'
                ], 403);
            }
            abort(403, 'Bạn không có quyền xóa admin khác!');
        }

        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Người dùng đã được xóa thành công!'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được xóa thành công!');
    }

    /**
     * Ban/Unban user
     */
    public function ban(Request $request, User $user)
    {

        $request->validate([
            'ban_types' => 'array',
            'ban_types.*' => 'in:login,comment,rate,read,ip'
        ], [
            'ban_types.array' => 'Loại ban phải là mảng',
            'ban_types.*.in' => 'Loại ban không hợp lệ',
        ]);

        // Không cho ban Super Admin
        if ($this->isUserSuperAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể ban Super Admin!'
            ], 403);
        }

        // Lấy ban hiện tại hoặc tạo mới
        $ban = UserBan::firstOrCreate(['user_id' => $user->id]);

        // Cập nhật tất cả các loại ban (cho phép mảng rỗng để unban tất cả)
        $banTypes = $request->ban_types ?? [];
        $updateData = [
            'login' => in_array('login', $banTypes),
            'comment' => in_array('comment', $banTypes),
            'rate' => in_array('rate', $banTypes),
            'read' => in_array('read', $banTypes),
        ];

        $ban->update($updateData);

        // Xử lý ban IP
        if (in_array('ip', $banTypes)) {
            // Ban IP
            if ($user->ip_address) {
                BanIp::firstOrCreate([
                    'ip_address' => $user->ip_address,
                    'user_id' => $user->id
                ]);
            }
        } else {
            // Unban IP
            if ($user->banIp) {
                $user->banIp->delete();
            }
        }

        // Nếu tất cả đều false, xóa record
        if (!$ban->login && !$ban->comment && !$ban->rate && !$ban->read) {
            $ban->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Trạng thái ban đã được cập nhật thành công!'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Trạng thái ban đã được cập nhật thành công!');
    }

    /**
     * Get ban info for user
     */
    public function getBanInfo(User $user)
    {

        $ban = $user->userBan;
        $banIp = $user->banIp;
        
        return response()->json([
            'ban' => [
                'login' => $ban->login ?? false,
                'comment' => $ban->comment ?? false,
                'rate' => $ban->rate ?? false,
                'read' => $ban->read ?? false,
                'ip' => $banIp ? true : false,
            ]
        ]);
    }


    /**
     * Ban IP address
     */
    public function banIp(Request $request)
    {

        $request->validate([
            'ip_address' => 'required|ip',
            'user_id' => 'nullable|exists:users,id'
        ], [
            'ip_address.required' => 'IP là bắt buộc',
            'ip_address.ip' => 'IP không hợp lệ',
            'user_id.exists' => 'Người dùng không tồn tại',
        ]);

        BanIp::create([
            'ip_address' => $request->ip_address,
            'user_id' => $request->user_id
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'IP đã được ban thành công!'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'IP đã được ban thành công!');
    }

    /**
     * Unban IP address
     */
    public function unbanIp(BanIp $banIp)
    {

        $banIp->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'IP đã được unban thành công!'
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'IP đã được unban thành công!');
    }

    /**
     * Gán role với expires_at cho VIP
     */
    private function assignRoleWithExpiry($user_id, $role_name)
    {
        DB::table('model_has_roles')->where([
            'model_id' => $user_id,
            'model_type' => 'App\\Models\\User',
        ])->delete();

        $role = Role::where('name', $role_name)->first();
        if (!$role) {
            return;
        }

        $role_id = $role->id;

        $data_insert = [
            'model_id'   => $user_id,
            'model_type' => 'App\\Models\\User',
            'role_id'    => $role_id,
        ];

        if ($role_name === 'VIP') {
            $data_insert['expires_at'] = now()->addDays(30);
        }

        DB::table('model_has_roles')->insert($data_insert);
    }
}