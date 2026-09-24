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

    /**
     * Menampilkan dashboard admin area dengan data nyata dari database,
     * difilter berdasarkan unit_induk user yang login.
     */
    public function indexArea()
    {
        $user = auth()->user();
        $unitInduk = $user->unit_induk;

        // ---- KPI CARDS (hanya pegawai & IDP di unit area ini) ----

        // 1. IDP Aktif di area ini
        $idpAktif = IDP::whereHas('bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->whereHas('monitoring', fn ($q) => $q->whereIn('status_perencanaan', ['Berjalan', 'Disetujui', 'Selesai']))
            ->count();
        $totalIdp = IDP::whereHas('bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))->count();

        // 2. Rata-rata Progres IDP di area ini
        $rataProgres = MonitoringIDP::whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->whereNotNull('progress_percent')
            ->avg('progress_percent') ?? 0;

        // 3. Coaching Selesai di area ini
        $coachingSelesai = MonitoringCoaching::whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->where('status', 'Disetujui')
            ->count();
        $totalCoaching = MonitoringCoaching::whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->count();

        // 4. Total Pegawai Aktif di area ini
        $totalPegawai = Pengguna::where('unit_induk', $unitInduk)->where('status_aktif', true)->count();
        $totalPegawaiAll = Pengguna::where('unit_induk', $unitInduk)->count();

        // ---- DISTRIBUSI PEGAWAI PER ROLE (di area ini) ----
        $labelRole = [
            'admin_area' => 'Admin Area',
            'atasan' => 'Atasan / Mentor',
            'bawahan' => 'Bawahan / Mentee',
        ];
        $distribusiUnit = Pengguna::select('role', DB::raw('count(*) as total'))
            ->where('unit_induk', $unitInduk)
            ->where('status_aktif', true)
            ->groupBy('role')
            ->orderBy('role')
            ->get()
            ->map(function ($item) use ($totalPegawai, $labelRole) {
                $item->unit_induk = $labelRole[$item->role] ?? ucfirst($item->role);
                $item->persentase = $totalPegawai > 0 ? round(($item->total / $totalPegawai) * 100, 1) : 0;
                return $item;
            });

        // ---- DISTRIBUSI PERIODE IDP (di area ini) ----
        $distribusiRole = IDP::select('periode_idp', DB::raw('count(*) as total'))
            ->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->groupBy('periode_idp')
            ->orderBy('periode_idp')
            ->get();

        // ---- STATUS IDP BREAKDOWN (di area ini, untuk chart) ----
        $statusIdp = MonitoringIDP::select('status_perencanaan', DB::raw('count(*) as total'))
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->groupBy('status_perencanaan')
            ->orderBy('status_perencanaan')
            ->get();

        // ---- QUICK ACTION / TODO (hanya untuk area ini) ----
        $todoRencanaReview = RencanaPengembanganIDP::whereIn('status', ['Diajukan', 'Revisi'])
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->count();
        $todoCoachingReview = CoachingBukti::where('status_atasan', 'pending')
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->count();
        $todoCoachingMenunggu = MonitoringCoaching::where('status', 'Menunggu Persetujuan')
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
            ->count();

        // ---- AKTIVITAS TERBARU (di area ini) ----
        $aktivitas = collect();

        RencanaPengembanganIDP::with(['daftarIdp.bawahan'])
            ->whereIn('status', ['Diajukan', 'Revisi'])
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
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
                    'url' => route('admin-area.idp.penetapan'),
                ]);
            });

        CoachingBukti::with(['daftarIdp.bawahan'])
            ->where('status_atasan', 'pending')
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
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
                    'url' => route('admin-area.coaching.pemantauan'),
                ]);
            });

        MonitoringCoaching::with('daftarIdp.bawahan')
            ->where('status', 'Disetujui')
            ->whereHas('daftarIdp.bawahan', fn ($q) => $q->where('unit_induk', $unitInduk))
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
                    'url' => route('admin-area.coaching.pemantauan'),
                ]);
            });

        $aktivitas = $aktivitas->sortByDesc('waktu')->take(8)->values();

        return view('admin-area.dashboard', compact(
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
