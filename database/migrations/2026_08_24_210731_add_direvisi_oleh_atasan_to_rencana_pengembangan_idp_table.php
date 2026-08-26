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
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->boolean('direvisi_oleh_atasan')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rencana_pengembangan_idp', function (Blueprint $table) {
            $table->dropColumn('direvisi_oleh_atasan');
        });
    }
};
