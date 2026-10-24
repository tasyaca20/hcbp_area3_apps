<?php

namespace App\Http\Middleware;

use App\Models\Notifikasi;
use App\Models\Pengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BangunRingkasanNotifikasi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof Pengguna && $request->isMethod('GET')) {
            try {
                match ($user->role) {
                    'admin_area' => Notifikasi::buatRingkasanUnit($user),
                    'admin_master' => Notifikasi::buatRingkasanHarian($user),
                    default => null,
                };
            } catch (\Throwable) {
                // ringkasan harian tidak boleh menggagalkan request halaman
            }
        }

        return $next($request);
    }
}
