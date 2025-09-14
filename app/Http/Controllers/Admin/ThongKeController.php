<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\Controller;

class ThongKeController extends Controller
{
    public function truyCap()
    {
        $onlineUsers = DB::table('online_users')
            ->where('last_activity', '>=', now()->subMinutes(30))
            ->count();

        $requestCount = DB::table('request_logs')->count();

        return response()->json([
            'online' => $onlineUsers,
            'requests' => $requestCount
        ]);
    }
}



