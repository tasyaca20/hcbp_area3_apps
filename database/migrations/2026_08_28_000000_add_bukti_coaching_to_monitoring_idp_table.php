<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_idp', function (Blueprint $table) {
            $table->string('bukti_pembelajaran_10_persen')->nullable();
            $table->string('bukti_social_learning_20_persen')->nullable();
            $table->string('bukti_experimental_learning_70_persen')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_idp', function (Blueprint $table) {
            $table->dropColumn([
                'bukti_pembelajaran_10_persen',
                'bukti_social_learning_20_persen',
                'bukti_experimental_learning_70_persen',
            ]);
        });
    }
};
