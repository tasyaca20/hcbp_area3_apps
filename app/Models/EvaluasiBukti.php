<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiBukti extends Model
{
    protected $table = 'evaluasi_bukti';

    protected $primaryKey = 'id_evaluasi_bukti';

    public $timestamps = true;

    protected $fillable = [
        'id_daftar_idp',
        'id_evaluasi',
        'jenis',
        'file_path',
        'original_name',
        'status_atasan',
        'catatan_revisi',
    ];

    protected $casts = [
        'jenis' => 'integer',
    ];

    public function daftarIdp()
    {
        return $this->belongsTo(IDP::class, 'id_daftar_idp', 'id_daftar_idp');
    }

    public function evaluasi()
    {
        return $this->belongsTo(Evaluasi::class, 'id_evaluasi', 'id_evaluasi');
    }
}
