<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

// Modul 6 (User Management) - Bagian 6.3: middleware ini sudah tersedia, cukup dipahami (tidak perlu diubah)
class DBDynamicConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!in_array($user->role, ['kasir', 'admin'])) {
                return $next($request);
            }

            $targetConnection = $user->role === 'kasir' ? 'mysql_kasir' : 'mysql_admin';

            if ($targetConnection && Config::has("database.connections.{$targetConnection}")) {
                try {
                    DB::connection($targetConnection)->getPdo();

                    Config::set('database.default', $targetConnection);
                    DB::purge($targetConnection);
                    DB::setDefaultConnection($targetConnection);
                } catch (\Throwable $e) {
                    Config::set('database.default', 'mysql');
                    DB::setDefaultConnection('mysql');
                }
            }
        }

        return $next($request);
    }
}
