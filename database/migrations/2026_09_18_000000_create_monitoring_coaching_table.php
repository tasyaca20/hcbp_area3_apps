<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_coaching', function (Blueprint $table) {
            $table->id('id_monitoring_coaching');
            $table->unsignedInteger('id_daftar_idp')->unique();
            $table->enum('status', ['Belum Mengisi Sesi Coaching', 'Menunggu Persetujuan', 'Disetujui'])->default('Belum Mengisi Sesi Coaching');
            $table->timestamps();

            $table->foreign('id_daftar_idp')->references('id_daftar_idp')->on('daftar_idp')->cascadeOnDelete();
        });

        $now = now();
        DB::table('daftar_idp')->orderBy('id_daftar_idp')->get()->each(function ($idp) use ($now) {
            $plans = DB::table('rencana_pengembangan_idp')
                ->where('id_daftar_idp', $idp->id_daftar_idp)
                ->where('status', 'Disetujui')
                ->pluck('id_rencana');
            $required = $plans->count() * 3;
            $bukti = $required ? DB::table('coaching_bukti')->whereIn('id_rencana', $plans)->get(['jenis', 'status_atasan']) : collect();
            $status = ! $required || $bukti->count() < $required
                ? 'Belum Mengisi Sesi Coaching'
                : ($bukti->every(fn ($item) => $item->status_atasan === 'setuju') ? 'Disetujui' : 'Menunggu Persetujuan');

            DB::table('monitoring_coaching')->insert([
                'id_daftar_idp' => $idp->id_daftar_idp,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, 'id_daftar_idp');
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_coaching');
    }
};
