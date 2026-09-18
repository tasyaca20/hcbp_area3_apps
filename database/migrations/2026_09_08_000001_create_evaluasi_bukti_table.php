<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('evaluasi_bukti');

        Schema::create('evaluasi_bukti', function (Blueprint $table) {
            $table->id('id_evaluasi_bukti');
            $table->integer('id_daftar_idp');
            $table->unsignedBigInteger('id_evaluasi');
            $table->tinyInteger('jenis')->comment('10=10%, 20=20%, 70=70%');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('status_atasan')->default('pending');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();

            $table->foreign('id_evaluasi')->references('id_evaluasi')->on('evaluasi')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_bukti');
    }
};
