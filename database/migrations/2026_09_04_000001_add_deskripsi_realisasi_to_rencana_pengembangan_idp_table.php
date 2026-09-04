<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->text('deskripsi_realisasi_10')->nullable()->after('deskripsi_coaching');
            $table->text('deskripsi_realisasi_20')->nullable()->after('deskripsi_realisasi_10');
            $table->text('deskripsi_realisasi_70')->nullable()->after('deskripsi_realisasi_20');
        });
    }

    public function down(): void
    {
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi_realisasi_10',
                'deskripsi_realisasi_20',
                'deskripsi_realisasi_70',
            ]);
        });
    }
};
