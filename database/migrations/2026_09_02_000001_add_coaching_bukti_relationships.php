<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE coaching_bukti MODIFY id_daftar_idp INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE coaching_bukti MODIFY id_rencana INT UNSIGNED NULL');
        DB::statement('ALTER TABLE coaching_bukti ADD CONSTRAINT coaching_bukti_idp_fk FOREIGN KEY (id_daftar_idp) REFERENCES daftar_idp(id_daftar_idp) ON DELETE CASCADE');
        DB::statement('ALTER TABLE coaching_bukti ADD CONSTRAINT coaching_bukti_rencana_fk FOREIGN KEY (id_rencana) REFERENCES rencana_pengembangan_idp(id_rencana) ON DELETE CASCADE');
        DB::statement('ALTER TABLE coaching_bukti ADD UNIQUE coaching_bukti_rencana_jenis_unique (id_rencana, jenis)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE coaching_bukti DROP INDEX coaching_bukti_rencana_jenis_unique');
        DB::statement('ALTER TABLE coaching_bukti DROP FOREIGN KEY coaching_bukti_rencana_fk');
        DB::statement('ALTER TABLE coaching_bukti DROP FOREIGN KEY coaching_bukti_idp_fk');
        DB::statement('ALTER TABLE coaching_bukti MODIFY id_daftar_idp INT NOT NULL');
    }
};
