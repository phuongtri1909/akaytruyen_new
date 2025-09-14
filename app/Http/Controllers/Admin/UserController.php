<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Requests\Admin\CreateEditUser;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

use App\Models\Donation;
class UserController extends Controller
{

    public function __construct(
        protected UserRepositoryInterface $repository,
        protected UserService             $service
    )
    {
        $this->middleware('can:xem_danh_sach_nguoi_dung')->only('index');
        $this->middleware('can:them_nguoi_dung')->only('store');
        $this->middleware('can:sua_nguoi_dung')->only('edit', 'update');
        $this->middleware('can:xoa_nguoi_dung')->only('destroy');
        $this->middleware('can:switch_user')->only('switchUserChange', 'switchUserBack');
    }

    // public function index(Request $request)
    // {
    //     $search        = $request->get('search', []);
    //     $user_inactive = User::query()->where('status', 0)->count();
    //     $roles         = Role::all();
    //     $users         = $this->service->getTable(20, [], $search);
    //     $donations = Donation::orderBy('donated_at', 'desc')->paginate(20); // Lấy 20 donate mới nhất
    //         // Lấy tổng số tiền donate của tất cả user
    //     $totalUserDonations = User::sum('donate_amount');
    //     // Lấy tổng doanh thu theo từng tháng và cộng thêm tổng donate của tất cả user
    //     $monthlyRevenue = Donation::selectRaw('MONTH(donated_at) as month, YEAR(donated_at) as year, SUM(amount) as total')
    //         ->groupBy('month', 'year')
    //         ->orderBy('year', 'desc')
    //         ->orderBy('month', 'desc')
    //         ->get()
    //         ->map(function ($revenue) use ($totalUserDonations) {
    //             $revenue->total += $totalUserDonations;
    //             return $revenue;
    //         });



    //     return view('Admin.pages.users.index', compact('users', 'roles', 'user_inactive','donations', 'monthlyRevenue','totalUserDonations'));
    // }
        public function index(Request $request)
    {
        $search        = $request->get('search', []);
        $user_inactive = User::query()->where('status', 2)->count();
        $roles         = Role::all();
        $users         = $this->service->getTable(20, [], $search);

        // Lấy tổng doanh thu theo từng tháng (bao gồm cả donation và user donate)
        $monthlyRevenue = Donation::selectRaw('MONTH(donated_at) as month, YEAR(donated_at) as year, SUM(amount) as total')
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($revenue) {
                // Thêm tổng donate từ user vào mỗi tháng
                $userDonations = User::where('donate_amount', '>', 0)
                    ->whereMonth('updated_at', $revenue->month)
                    ->whereYear('updated_at', $revenue->year)
                    ->sum('donate_amount');
                $revenue->total += $userDonations;
                return $revenue;
            });

        return view('Admin.pages.users.index', compact('users', 'roles', 'user_inactive', 'monthlyRevenue'));
    }

    public function create(Request $request)
    {
        $user                  = $this->repository->getModel();
        $user_id               = 0;
        $formOptions           = $this->service->formOptions($user);
        $formOptions['action'] = route('admin.users.store');
        $default_values        = $formOptions['default_values'];

        $view_data = compact('formOptions', 'default_values', 'user_id');

        return view('Admin.pages.users.add-edit', $view_data);
    }

    public function store(CreateEditUser $request)
    {
        // dd($request->input());
        // $inputs = $request->only(['name', 'email', 'role_id', 'status']);
        $inputs             = $request->all();
        $inputs['password'] = Hash::make($inputs['password']);

        $this->service->create($inputs);


        return redirect(route('admin.users.index'))
            ->with('successMessage', 'Thêm mới thành công.');
    }

    public function edit(Request $request, int $user_id)
    {
        $user                  = $this->repository->find($user_id, ['roles']);
        if (!$user) {
            return redirect()->route('admin.users.index')->with('errorMessage', 'Người dùng không tồn tại.');
        }

        $formOptions           = $this->service->formOptions($user);
        $formOptions['action'] = route('admin.users.update', $user_id);
        $default_values        = $formOptions['default_values'];

        $view_data = compact('formOptions', 'default_values', 'user_id', 'user'); // Thêm biến user

        return view('Admin.pages.users.add-edit', $view_data);
    }


    public function destroy($id)
{
    $user = User::find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'Người dùng không tồn tại.');
    }

    $user->delete();

    return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được xóa.');
}

public function update(CreateEditUser $request, int $user_id)
{
    $data = $request->all();

    // Nếu password trống, không cập nhật
    if (empty($data['password'])) {
        unset($data['password']);
    } else {
        $data['password'] = Hash::make($data['password']);
    }

    // Xử lý trạng thái hạn chế
    $data['ban_login'] = $request->has('ban_login');
    $data['ban_comment'] = $request->has('ban_comment');
    if (isset($data['donate_amount'])) {
        $data['donate_amount'] = max(0, floatval($data['donate_amount']));
    }
    // Cập nhật user
    $this->service->update($user_id, $data);
    // Gán role VIP
// 1. Xoá tất cả role hiện tại
DB::table('model_has_roles')->where([
    'model_id' => $user_id,
    'model_type' => 'App\\Models\\User',
])->delete();

// 2. Gán lại role mới được chọn
$role_id = $request->input('role_id');

$data_insert = [
    'model_id'   => $user_id,
    'model_type' => 'App\\Models\\User',
    'role_id'    => $role_id,
];

// Nếu là VIP thì thêm expires_at
if ($role_id == 6) {
    $data_insert['expires_at'] = now()->addDays(30);
}

DB::table('model_has_roles')->insert($data_insert);



// Xử lý cấm IP nếu user hiện tại là admin
if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Mod')) {
    // Lấy thông tin user bị cấm
    $user = User::find($user_id);

    if ($user) {
        if ($request->input('ban_ip', false)) {
            DB::table('banned_ips')->updateOrInsert(
                ['user_id' => $user_id],
                ['ip_address' => $user->ip_address, 'created_at' => now(), 'updated_at' => now()]
            );
        } else {
            DB::table('banned_ips')->where('user_id', $user_id)->delete();
        }
    }
}


    return redirect(route('admin.users.index'))
        ->with('successMessage', 'Thay đổi thành công.');
}

    public function redirectToProvider($provider)
    {
        $url_previous = URL::previous();
        $url_login    = URL::to(route('login'));
        $url_index    = URL::to(route('admin.dashboard.index'));

        if ($url_previous != $url_login) {
            session()->put('pre_url', $url_previous);
        } else {
            session()->put('pre_url', $url_index);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        $authUser = $this->findOrCreateUser($user, $provider);
        if (!$authUser) {
            return redirect(route('login'))->withErrors(['email' => 'Người dùng không tồn tại trong hệ thống, bạn liên hệ với quản trị viên để được hỗ trợ.']);
        }

        Auth::guard()->login($authUser, true);
        return redirect(Session::get('pre_url'));
    }

    public function findOrCreateUser($providerUser, $provider)
    {
        $user = $this->repository->getByEmail($providerUser->email);
        if (!$user) {
            return false;
        }
        if ($user->avatar != ($providerUser->avatar ?? '')) {
            $user->update(['avatar' => $providerUser->avatar]);
        }

        return $user;
    }

    public function switchUser(Request $request, int $user_id)
    {
        $user = Helper::currentUser();
        if ($user && $user->other_user && $user->other_user->id == $user_id) {
            Auth::login($user->other_user, true);
            $route = $this->routeFromUser($user->other_user);

            return redirect($route)->with('successMessage', 'Đổi tài khoản thành công.');
        }

        abort(403, 'Bạn không có quyền thực hiện chức năng này.');
    }

    public function switchUserChange(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string']
        ]);
        $email       = $request->input('email');
        $currentUser = Helper::currentUser();
        if ($email == $currentUser->email) {
            return back()->with('errorMessage', 'Bạn đang ở tài khoản này.');
        }
        $authUser = User::query()
            ->where(function ($query) use ($email) {
                if (str_contains($email, '@')) {
                    $query->where('email', $email);
                } else {
                    $query->where('name', $email);
                }
            })
            ->where('status', User::STATUS_ACTIVE)
            ->first();
        if ($authUser) {
            Auth::login($authUser, true);
            session()->put('switch_back', $currentUser->id);

            $route = $this->routeFromUser($authUser);

            return redirect($route)->with('successMessage', 'Đổi tài khoản thành công.');
        } else {
            return back()->with('errorMessage', 'Không tìm thấy tài khoản này.');
        }
    }

    public function switchUserBack(Request $request, int $userId)
    {
        $switchBack = session()->get('switch_back');
        if (!$switchBack || $switchBack != $userId) {
            abort(403);
        }
        $authUser = User::query()->where('id', $userId)->where('status', User::STATUS_ACTIVE)->first();
        if ($authUser) {
            session()->forget('switch_back');
            Auth::login($authUser, true);

            $route = $this->routeFromUser($authUser);

            return redirect($route)->with('successMessage', 'Đổi tài khoản thành công.');
        }
        return back()->with('errorMessage', 'Không tìm thấy tài khoản này.');
    }

    public function routeFromUser($user)
    {
//        if (($user?->roles?->first()?->name ?: '') == User::ROLE_TDV) {
//            return route('admin.tdv.dashboard');
//        }
//
//        if (($user?->roles?->first()?->name ?: '') == User::ROLE_THIEN_DIEU) {
//            return route('admin.thien_dieu.dashboard');
//        }

        return route('admin.dashboard.index');
    }
    public function getOnlineUsers()
    {
        $timeout = now()->subMinutes(60);

        $users = \DB::table('online_users')
            ->where('last_activity', '>=', $timeout)
            ->leftJoin('users', 'online_users.user_id', '=', 'users.id')
            ->select(
                \DB::raw("COALESCE(users.name, 'Khách') as name"),
                'online_users.ip as ip',
                'online_users.last_activity'
            )
            ->orderBy('last_activity', 'desc')
            ->distinct() // Tránh trùng dòng (ví dụ IP trùng)
            ->get();

        return response()->json(['users' => $users]);
    }




}
