<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        $password   = $request->input('password');

        // VERSI BERBAHAYA: input digabung langsung ke query (rentan SQL Injection)
        $sql = "SELECT * FROM users WHERE name = '{$loginInput}' AND password = '{$password}' LIMIT 1";
        try {
            $row = DB::selectOne($sql);
        } catch (\Throwable $e) {
            return back()
                ->with('error', 'Query Error: ' . $e->getMessage() . ' Query: ' . $sql)
                ->withInput($request->only('username'));
        }

        // VERSI AMAN: parameter binding (?), input tidak bisa mengubah query
        // try {
        //     $row = DB::selectOne(
        //         "SELECT * FROM users WHERE name = ? AND password = ? LIMIT 1",
        //         [$loginInput, $password]
        //     );
        // } catch (\Throwable $e) {
        //     Log::error($e->getMessage());
        //     return back()
        //         ->with('error', 'Terjadi kesalahan, silakan coba lagi.')
        //         ->withInput($request->only('username'));
        // }

        if ($row) {
            $user = User::find($row->id);
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
