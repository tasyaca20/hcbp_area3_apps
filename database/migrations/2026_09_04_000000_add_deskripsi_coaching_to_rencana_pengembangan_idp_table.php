<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->text('deskripsi_coaching')->nullable()->after('action_learning_70_persen');
        });
    }

    public function down(): void
    {
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->dropColumn('deskripsi_coaching');
        });
    }
};
