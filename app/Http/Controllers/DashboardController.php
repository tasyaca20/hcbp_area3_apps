<?php

namespace App\Http\Controllers;

use App\Models\CoachingBukti;
use App\Models\IDP;
use App\Models\MonitoringCoaching;
use App\Models\MonitoringIDP;
use App\Models\Pengguna;
use App\Models\RencanaPengembanganIDP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard admin master dengan data nyata dari database.
     */
    public function index()
    {
        // ---- KPI CARDS ----

        // 1. IDP Aktif: IDP yang sedang berjalan atau sudah disetujui/selesai
        $idpAktif = IDP::whereHas('monitoring', function ($q) {
            $q->whereIn('status_perencanaan', ['Berjalan', 'Disetujui', 'Selesai']);
        })->count();
        $totalIdp = IDP::count();

        // 2. Rata-rata Progres IDP: rata-rata progress_percent dari monitoring_idp
        $rataProgres = MonitoringIDP::whereNotNull('progress_percent')->avg('progress_percent') ?? 0;

        // 3. Coaching Selesai: jumlah monitoring_coaching dengan status Disetujui
        $coachingSelesai = MonitoringCoaching::where('status', 'Disetujui')->count();
        $totalCoaching = MonitoringCoaching::count();

        // 4. Total Pegawai Aktif
        $totalPegawai = Pengguna::where('status_aktif', true)->count();
        $totalPegawaiAll = Pengguna::count();

        // ---- DISTRIBUSI PEGAWAI PER UNIT INDUK ----
        $distribusiUnit = Pengguna::select('unit_induk', DB::raw('count(*) as total'))
            ->whereNotNull('unit_induk')
            ->where('status_aktif', true)
            ->groupBy('unit_induk')
            ->orderBy('unit_induk')
            ->get()
            ->map(function ($item) use ($totalPegawai) {
                $item->persentase = $totalPegawai > 0 ? round(($item->total / $totalPegawai) * 100, 1) : 0;
                return $item;
            });

        // ---- DISTRIBUSI ROLE ----
        $distribusiRole = Pengguna::select('role', DB::raw('count(*) as total'))
            ->where('status_aktif', true)
            ->groupBy('role')
            ->orderBy('role')
            ->get();

        // ---- STATUS IDP BREAKDOWN (untuk chart) ----
        $statusIdp = MonitoringIDP::select('status_perencanaan', DB::raw('count(*) as total'))
            ->groupBy('status_perencanaan')
            ->orderBy('status_perencanaan')
            ->get();

        // ---- QUICK ACTION / TODO ----
        $todoRencanaReview = RencanaPengembanganIDP::whereIn('status', ['Diajukan', 'Revisi'])->count();
        $todoCoachingReview = CoachingBukti::where('status_atasan', 'pending')->count();
        $todoCoachingMenunggu = MonitoringCoaching::where('status', 'Menunggu Persetujuan')->count();

        // ---- AKTIVITAS TERBARU ----
        // Gabungan beberapa sumber aktivitas, diurutkan berdasarkan waktu terbaru
        $aktivitas = collect();

        // Rencana IDP yang baru diajukan/direvisi
        RencanaPengembanganIDP::with(['daftarIdp.bawahan'])
            ->whereIn('status', ['Diajukan', 'Revisi'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($rencana) use ($aktivitas) {
                $nama = $rencana->daftarIdp && $rencana->daftarIdp->bawahan
                    ? $rencana->daftarIdp->bawahan->nama
                    : 'Pegawai';
                $aktivitas->push([
                    'tipe' => 'Rencana IDP',
                    'deskripsi' => $nama . ' mengajukan rencana pengembangan IDP',
                    'status' => $rencana->status,
                    'waktu' => $rencana->updated_at,
                    'url' => route('admin-master.idp.penetapan'),
                ]);
            });

        // Bukti coaching baru diupload (pending review)
        CoachingBukti::with(['daftarIdp.bawahan'])
            ->where('status_atasan', 'pending')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($bukti) use ($aktivitas) {
                $nama = $bukti->daftarIdp && $bukti->daftarIdp->bawahan
                    ? $bukti->daftarIdp->bawahan->nama
                    : 'Pegawai';
                $aktivitas->push([
                    'tipe' => 'Bukti Coaching',
                    'deskripsi' => $nama . ' mengunggah bukti coaching',
                    'status' => 'Menunggu Review',
                    'waktu' => $bukti->updated_at,
                    'url' => route('admin-master.coaching.pemantauan'),
                ]);
            });

        // Coaching yang baru disetujui
        MonitoringCoaching::with('daftarIdp.bawahan')
            ->where('status', 'Disetujui')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($monitoring) use ($aktivitas) {
                $nama = $monitoring->daftarIdp && $monitoring->daftarIdp->bawahan
                    ? $monitoring->daftarIdp->bawahan->nama
                    : 'Pegawai';
                $aktivitas->push([
                    'tipe' => 'Coaching',
                    'deskripsi' => $nama . ' menyelesaikan sesi coaching',
                    'status' => 'Disetujui',
                    'waktu' => $monitoring->updated_at,
                    'url' => route('admin-master.coaching.pemantauan'),
                ]);
            });

        // Urutkan gabungan aktivitas berdasarkan waktu terbaru dan ambil 8
        $aktivitas = $aktivitas->sortByDesc('waktu')->take(8)->values();

        return view('admin-master.dashboard', compact(
            'idpAktif',
            'totalIdp',
            'rataProgres',
            'coachingSelesai',
            'totalCoaching',
            'totalPegawai',
            'totalPegawaiAll',
            'distribusiUnit',
            'distribusiRole',
            'statusIdp',
            'todoRencanaReview',
            'todoCoachingReview',
            'todoCoachingMenunggu',
            'aktivitas'
        ));
    }
}
