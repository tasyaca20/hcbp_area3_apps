<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingBukti extends Model
{
    protected $table = 'coaching_bukti';

    protected $primaryKey = 'id_coaching_bukti';

    public $timestamps = true;

    protected $fillable = [
        'id_daftar_idp',
        'id_rencana',
        'jenis',
        'file_path',
    ];

    protected $casts = [
        'jenis' => 'integer',
    ];

    public function daftarIdp()
    {
        return $this->belongsTo(IDP::class, 'id_daftar_idp', 'id_daftar_idp');
    }

    public function rencana()
    {
        return $this->belongsTo(RencanaPengembanganIDP::class, 'id_rencana', 'id_rencana');
    }
}
