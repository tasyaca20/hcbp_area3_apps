<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_pengguna',
        'tipe',
        'judul',
        'pesan',
        'tautan',
        'url',
        'id_daftar_idp',
        'dibaca_pada',
        'ringkasan_tanggal',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
            'ringkasan_tanggal' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    public function getTujuanAttribute(): ?string
    {
        return $this->url ?: $this->tautan;
    }

    public static function kirim(int $idPengguna, string $tipe, string $judul, string $pesan, ?string $url = null, ?int $idDaftarIdp = null): void
    {
        static::create([
            'id_pengguna' => $idPengguna,
            'tipe' => $tipe,
            'judul' => $judul,
            'pesan' => $pesan,
            'tautan' => $url,
            'url' => $url,
            'id_daftar_idp' => $idDaftarIdp,
        ]);
    }

    public static function keAdminAreaUnit(?string $unitInduk): Collection
    {
        if (! $unitInduk) {
            return collect();
        }

        return Pengguna::where('role', 'admin_area')->where('unit_induk', $unitInduk)->get(['id_pengguna']);
    }

    public static function buatRingkasanUnit(Pengguna $admin): void
    {
        if (! $admin->unit_induk) {
            return;
        }

        $hariIni = Carbon::today();
        $sudahAda = static::where('id_pengguna', $admin->id_pengguna)
            ->where('tipe', 'ringkasan_unit')
            ->whereDate('ringkasan_tanggal', $hariIni)
            ->exists();
        if ($sudahAda) {
            return;
        }

        $scope = fn ($query) => $query->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $admin->unit_induk));

        $menunggu = RencanaPengembanganIDP::where('status', 'Diajukan')->whereHas('daftarIdp', $scope)->count();
        $revisi = RencanaPengembanganIDP::where('status', 'Revisi')->whereHas('daftarIdp', $scope)->count();
        $evaluasi = Evaluasi::whereDate('tanggal_penilaian', $hariIni)->whereHas('daftarIdp', $scope)->count();

        if ($menunggu + $revisi + $evaluasi === 0) {
            return;
        }

        static::create([
            'id_pengguna' => $admin->id_pengguna,
            'tipe' => 'ringkasan_unit',
            'judul' => 'Ringkasan '.$admin->unit_induk,
            'pesan' => "{$menunggu} rencana menunggu tinjauan, {$revisi} permintaan revisi, {$evaluasi} evaluasi selesai hari ini.",
            'tautan' => route('admin-area.idp.pemantauan', absolute: false),
            'url' => route('admin-area.idp.pemantauan', absolute: false),
            'ringkasan_tanggal' => $hariIni,
        ]);
    }

    public static function buatRingkasanHarian(Pengguna $admin): void
    {
        $hariIni = Carbon::today();
        $sudahAda = static::where('id_pengguna', $admin->id_pengguna)
            ->where('tipe', 'ringkasan_harian')
            ->whereDate('ringkasan_tanggal', $hariIni)
            ->exists();
        if ($sudahAda) {
            return;
        }

        $menunggu = RencanaPengembanganIDP::where('status', 'Diajukan')->count();
        $revisi = RencanaPengembanganIDP::where('status', 'Revisi')->count();
        $evaluasi = Evaluasi::whereDate('tanggal_penilaian', $hariIni)->count();

        if ($menunggu + $revisi + $evaluasi === 0) {
            return;
        }

        static::create([
            'id_pengguna' => $admin->id_pengguna,
            'tipe' => 'ringkasan_harian',
            'judul' => 'Ringkasan harian',
            'pesan' => "{$menunggu} rencana menunggu tinjauan, {$revisi} permintaan revisi, {$evaluasi} evaluasi selesai hari ini.",
            'tautan' => route('admin-master.idp.pemantauan', absolute: false),
            'url' => route('admin-master.idp.pemantauan', absolute: false),
            'ringkasan_tanggal' => $hariIni,
        ]);
    }
}
