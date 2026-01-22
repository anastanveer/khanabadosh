<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login', [
            'pageTitle' => 'Admin Login',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = env('ADMIN_USER', 'admin');
        $pass = env('ADMIN_PASS', 'password');

        if (hash_equals((string) $user, (string) $data['email'])
            && hash_equals((string) $pass, (string) $data['password'])) {
            $request->session()->put('admin_authenticated', true);
            $request->session()->regenerate();

            return redirect()->route('admin.index');
        }

        return back()
            ->withErrors(['email' => 'Invalid credentials.'])
            ->withInput(['email' => $data['email']]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
