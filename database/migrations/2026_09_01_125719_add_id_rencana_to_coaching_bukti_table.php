<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coaching_bukti', function (Blueprint $table) {
            if (!Schema::hasColumn('coaching_bukti', 'id_rencana')) {
                $table->unsignedBigInteger('id_rencana')->nullable()->after('id_daftar_idp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaching_bukti', function (Blueprint $table) {
            if (Schema::hasColumn('coaching_bukti', 'id_rencana')) {
                $table->dropColumn('id_rencana');
            }
        });
    }
};
