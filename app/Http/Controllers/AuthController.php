<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Tidak ada perubahan yang diperlukan di file ini.
class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('username'));

        $user = User::whereRaw('LOWER(name) = ?', [strtolower($loginInput)])
            ->orWhereRaw('LOWER(role) = ?', [strtolower($loginInput)])
            ->orWhere('email', $loginInput)
            ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return $this->redirectByRole($user)
                ->with('success', "Berhasil masuk sebagai {$user->name}.");
        }

        return back()
            ->with('error', 'Username atau password salah.')
            ->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Berhasil logout.');
    }

    public function switchUser(Request $request, $role = null)
    {
        $role = $role ?? $request->input('role');

        if ($role === 'guest') {
            Auth::logout();
            return redirect()->route('home')->with('info', 'Berhasil logout.');
        }

        $user = User::where('role', $role)->first();

        if (!$user) {
            return redirect()->back()->with('error', "User '{$role}' tidak ditemukan.");
        }

        Auth::login($user);

        return $this->redirectByRole($user)
            ->with('info', "Masuk sebagai {$user->name}.");
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'kasir' => redirect()->route('kasir.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
