<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('coaching_bukti', 'status_atasan')) {
            DB::statement('ALTER TABLE coaching_bukti MODIFY status_atasan VARCHAR(20) NULL');
            DB::table('coaching_bukti')->where('status_atasan', 'Disetujui')->update(['status_atasan' => 'setuju']);
            DB::table('coaching_bukti')->whereNull('status_atasan')->update(['status_atasan' => 'pending']);
            DB::statement("ALTER TABLE coaching_bukti MODIFY status_atasan VARCHAR(20) NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('coaching_bukti', function (Blueprint $table) {
                $table->string('status_atasan', 20)->default('pending')->after('original_name');
            });
        }

        if (Schema::hasColumn('coaching_bukti', 'catatan_atasan')) {
            Schema::table('coaching_bukti', function (Blueprint $table) {
                $table->renameColumn('catatan_atasan', 'catatan_revisi');
            });
        } elseif (! Schema::hasColumn('coaching_bukti', 'catatan_revisi')) {
            Schema::table('coaching_bukti', function (Blueprint $table) {
                $table->text('catatan_revisi')->nullable()->after('status_atasan');
            });
        }
    }

    public function down(): void
    {
        Schema::table('coaching_bukti', function (Blueprint $table) {
            $table->dropColumn(['status_atasan', 'catatan_revisi']);
        });
    }
};
