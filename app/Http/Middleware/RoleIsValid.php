<?php

namespace App\Http\Middleware;

use App\Models\AdminRoleUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(AdminRoleUser::where('user_id', Auth::user()->id)->first()->role_id !== 1) {
            return redirect()->back();
        };
        return $next($request);
    }
}
