<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('admin_authenticated')) {
            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
