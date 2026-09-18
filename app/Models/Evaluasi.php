<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasi';

    protected $primaryKey = 'id_evaluasi';

    public $timestamps = true;

    protected $fillable = [
        'id_daftar_idp',
        'id_rencana_asal',
        'id_kompetensi',
        'pembelajaran_10_persen',
        'social_learning_20_persen',
        'action_learning_70_persen',
        'deskripsi_realisasi_10',
        'deskripsi_realisasi_20',
        'deskripsi_realisasi_70',
        'skor',
        'feedback_evaluasi',
        'tanggal_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penilaian' => 'date',
        ];
    }

    public function daftarIdp()
    {
        return $this->belongsTo(IDP::class, 'id_daftar_idp', 'id_daftar_idp');
    }

    public function kompetensi()
    {
        return $this->belongsTo(Kompetensi::class, 'id_kompetensi', 'id_kompetensi');
    }

    public function bukti()
    {
        return $this->hasMany(EvaluasiBukti::class, 'id_evaluasi', 'id_evaluasi');
    }
}
