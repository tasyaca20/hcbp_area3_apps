<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id('id_notifikasi');
                $table->integer('id_pengguna');
                $table->string('tipe', 40);
                $table->string('judul', 150);
                $table->text('pesan');
                $table->string('url')->nullable();
                $table->integer('id_daftar_idp')->nullable();
                $table->timestamp('dibaca_pada')->nullable();
                $table->date('ringkasan_tanggal')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('notifikasi', function (Blueprint $table) {
            if (! Schema::hasColumn('notifikasi', 'tipe')) {
                $table->string('tipe', 40)->default('umum');
            }
            if (! Schema::hasColumn('notifikasi', 'judul')) {
                $table->string('judul', 150)->default('');
            }
            if (! Schema::hasColumn('notifikasi', 'url')) {
                $table->string('url')->nullable();
            }
            if (! Schema::hasColumn('notifikasi', 'id_daftar_idp')) {
                $table->integer('id_daftar_idp')->nullable();
            }
            if (! Schema::hasColumn('notifikasi', 'ringkasan_tanggal')) {
                $table->date('ringkasan_tanggal')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
