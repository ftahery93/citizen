<?php

namespace App\Http\Middleware;

use App\Models\Admin\User;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {

        if (!$request->user()->hasRolePermission($permission)) {
            return redirect('admin/errors/401');
        }
        return $next($request);

    }
}
