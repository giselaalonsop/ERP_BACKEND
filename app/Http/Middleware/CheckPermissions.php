<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if ($user && $user->rol == 'admin') {
            // Admin has access to everything
            return $next($request);
        }

        if ($user && $user->permissions) {
            $permissions = json_decode($user->permissions, true);

            if (isset($permissions[$permission]) && $permissions[$permission]) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 403);
    }
}
