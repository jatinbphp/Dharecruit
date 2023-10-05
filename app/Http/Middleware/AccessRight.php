<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessRight
{
    public function handle(Request $request, Closure $next, $access): Response
    {
        $userRole = Auth::user()->role;
        if($userRole == 'admin'){
            return $next($request);
        }
        $permission = Permission::where('type', $userRole)->first();
        if(!empty($permission)){
            $right = explode(',', $permission->access_modules);
            $check = in_array($access, $right) ? 1 : 0;
            if($check == 1 || $permission['type'] == 'admin'){
                return $next($request);
            }
        }
        return redirect('/admin');
    }
}
