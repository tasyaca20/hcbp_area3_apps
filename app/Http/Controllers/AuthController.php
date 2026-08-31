<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\ViewServiceProvider;

class AuthController extends Controller
{
    public function showLogin()
    {
        return response()->json([
            'view_bound' => app()->bound('view'),
            'view_provider_loaded' => count(app()->getProviders(ViewServiceProvider::class)) > 0,
            'view_provider_class' => class_exists(ViewServiceProvider::class),
            'app_providers_contains_view' => in_array(ViewServiceProvider::class, config('app.providers', []), true),
            'view_config_exists' => config()->has('view'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $pengguna = Pengguna::where('username', $credentials['username'])
            ->where('status_aktif', true)
            ->first();

        if (!$pengguna || !Hash::check($credentials['password'], $pengguna->password_hash)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username');
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return match ($pengguna->role) {
            'admin_master' => redirect()->route('admin-master.dashboard'),
            'admin_area' => redirect()->route('admin-area.dashboard'),
            'atasan' => redirect()->route('atasan.idp.daftar'),
            'bawahan' => redirect()->route('bawahan.dashboard'),
            default => redirect()->route('login'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
