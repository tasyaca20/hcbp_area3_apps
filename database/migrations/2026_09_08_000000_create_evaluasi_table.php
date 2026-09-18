<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('evaluasi');

        Schema::create('evaluasi', function (Blueprint $table) {
            $table->id('id_evaluasi');
            $table->integer('id_daftar_idp');
            $table->integer('id_rencana_asal')->nullable()->comment('id rencana_pengembangan_idp sumber (dari coaching)');
            $table->integer('id_kompetensi')->nullable();
            $table->text('pembelajaran_10_persen')->nullable();
            $table->text('social_learning_20_persen')->nullable();
            $table->text('action_learning_70_persen')->nullable();
            $table->text('deskripsi_realisasi_10')->nullable();
            $table->text('deskripsi_realisasi_20')->nullable();
            $table->text('deskripsi_realisasi_70')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};
