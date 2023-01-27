<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insiden extends Model
{
    protected $table = 'ppi';

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
            'ppi.RUANGAN AS ruangan',
        );
    }

    public function scopePasien($query)
    {
        return $query->join('m_pasien', 'm_pasien.MR', '=', 'ppi.MR')
            ->addSelect('m_pasien.NAMA as nama');
    }
}
