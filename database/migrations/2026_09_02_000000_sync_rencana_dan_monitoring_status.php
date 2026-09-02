<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE rencana_pengembangan_idp MODIFY status ENUM('Draft', 'Diajukan', 'Revisi', 'Disetujui', 'Berjalan', 'Selesai') NOT NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE monitoring_idp MODIFY status_perencanaan ENUM('Draft', 'Diajukan', 'Revisi', 'Disetujui', 'Berjalan', 'Selesai') NOT NULL DEFAULT 'Draft'");
        DB::statement("UPDATE monitoring_idp monitoring JOIN (SELECT id_daftar_idp, CASE WHEN SUM(status = 'Revisi') > 0 THEN 'Revisi' WHEN SUM(status = 'Diajukan') > 0 THEN 'Diajukan' WHEN SUM(status = 'Disetujui') > 0 THEN 'Disetujui' ELSE 'Draft' END AS status FROM rencana_pengembangan_idp GROUP BY id_daftar_idp) rencana ON rencana.id_daftar_idp = monitoring.id_daftar_idp SET monitoring.status_perencanaan = rencana.status");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE rencana_pengembangan_idp MODIFY status ENUM('Draft', 'Diajukan', 'Disetujui', 'Revisi') NOT NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE monitoring_idp MODIFY status_perencanaan ENUM('Diajukan', 'Revisi', 'Disetujui', 'Berjalan', 'Selesai') NOT NULL DEFAULT 'Diajukan'");
    }
};
