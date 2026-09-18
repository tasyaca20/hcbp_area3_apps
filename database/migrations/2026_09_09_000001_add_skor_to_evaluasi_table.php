<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->tinyInteger('skor')->nullable()->after('deskripsi_realisasi_70');
            $table->text('feedback_evaluasi')->nullable()->after('skor');
            $table->date('tanggal_penilaian')->nullable()->after('feedback_evaluasi');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropColumn(['skor', 'feedback_evaluasi', 'tanggal_penilaian']);
        });
    }
};
