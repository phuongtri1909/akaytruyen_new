<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBan
{
    public function handle(Request $request, Closure $next, $action = null): Response
    {
        $user = auth()->user();

        if ($user) {
            if ($user->userBan) {
                if ($action === 'login' && $user->userBan->login) {

                    if($request->ajax()){
                        return response()->json(['message' => 'Tài khoản của bạn đã bị cấm đăng nhập.'], 403);
                    }

                    abort(403, 'Tài khoản của bạn đã bị cấm đăng nhập.');
                }
                if ($action === 'comment' && $user->userBan->comment) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm bình luận.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm bình luận.');
                }
                if ($action === 'rate' && $user->userBan->rate) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm đánh giá.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm đánh giá.');
                }
                if ($action === 'read' && $user->userBan->read) {
                    if($request->ajax()){
                        return response()->json(['message' => 'Bạn đã bị cấm đọc nội dung.'], 403);
                    }
                    abort(403, 'Bạn đã bị cấm đọc nội dung.');
                }
            }

            $ip = $request->ip();
            if ($user->banIps()->where('ip_address', $ip)->exists()) {
                sleep(10);
                if($request->ajax()){
                    return response()->json(['message' => 'IP của bạn đã bị cấm.'], 403);
                }
                abort(403);
            }
        }

        return $next($request);
    }
}
