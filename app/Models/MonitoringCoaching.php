<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringCoaching extends Model
{
    protected $table = 'monitoring_coaching';

    protected $primaryKey = 'id_monitoring_coaching';

    protected $fillable = [
        'id_daftar_idp',
        'status',
    ];

    public function daftarIdp()
    {
        return $this->belongsTo(IDP::class, 'id_daftar_idp', 'id_daftar_idp');
    }
}
