<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAnyPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        if (!auth()->check()) {
            abort(403, 'Bạn không có quyền truy cập trang này!');
        }

        if (auth()->user()->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập trang này!');
    }
}
