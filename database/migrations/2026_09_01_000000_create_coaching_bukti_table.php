<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('coaching_bukti');
        
        Schema::create('coaching_bukti', function (Blueprint $table) {
            $table->id('id_coaching_bukti');
            $table->integer('id_daftar_idp');
            $table->tinyInteger('jenis')->comment('10=10%, 20=20%, 70=70%');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_bukti');
    }
};
