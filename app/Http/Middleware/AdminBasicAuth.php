<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminBasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = env('ADMIN_USER', 'admin');
        $pass = env('ADMIN_PASS', 'password');

        if ($request->getUser() === $user && $request->getPassword() === $pass) {
            return $next($request);
        }

        return response('Unauthorized', 401)
            ->header('WWW-Authenticate', 'Basic realm="Admin Area"');
    }
}
