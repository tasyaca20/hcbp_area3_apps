<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $daftar = Notifikasi::where('id_pengguna', auth()->id())
            ->orderByDesc('id_notifikasi')
            ->paginate(20);

        return view('notifikasi.index', compact('daftar'));
    }

    public function baca(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->id_pengguna === auth()->id(), 403);
        $notifikasi->update(['dibaca_pada' => now()]);

        return $notifikasi->tujuan ? redirect($notifikasi->tujuan) : back();
    }

    public function bacaSemua()
    {
        Notifikasi::where('id_pengguna', auth()->id())->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
