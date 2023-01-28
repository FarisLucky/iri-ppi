<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Insiden extends Model
{
    const VERIFIED = 1;

    protected $table = 'ppi';

    protected $fillable = [
        'IDO',
        'ISK',
        'LMINFUS',
        'LMKTT',
        'LMRAWAT',
        'PLEBITIS',
        'VERIFIED',
        'UPDATED_AT',
    ];

    public function scopeSelectInsiden($query)
    {
        return $query->select(
            'ppi.TANGGAL AS tanggal',
            'ppi.LMRAWAT AS lmrawat',
            'ppi.LMINFUS AS lminfus',
            'ppi.LMINFUS AS lmktt',
            'ppi.PLEBITIS AS plebitis',
            'ppi.ISK AS isk',
            'ppi.IDO AS ido',
            'ppi.MR AS mr',
            'ppi.ID AS id',
            'ppi.RUANGAN AS ruangan',
        );
    }

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function scopeJoinPasien($query)
    {
        return $query->join('m_pasien', 'm_pasien.MR', '=', 'ppi.MR')
            ->addSelect('m_pasien.NAMA as nama');
    }
}
