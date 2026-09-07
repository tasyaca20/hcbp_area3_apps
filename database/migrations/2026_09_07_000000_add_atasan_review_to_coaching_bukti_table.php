<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaching_bukti', function (Blueprint $table) {
            $table->string('status_atasan', 20)->default('pending')->after('original_name');
            $table->text('catatan_revisi')->nullable()->after('status_atasan');
        });
    }

    public function down(): void
    {
        Schema::table('coaching_bukti', function (Blueprint $table) {
            $table->dropColumn(['status_atasan', 'catatan_revisi']);
        });
    }
};
