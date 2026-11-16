<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakModel extends Model
{
    protected $table            = 'anak';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'nik_anak',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'berat_badan_lahir',
        'panjang_badan_lahir',
        'lingkar_kepala_lahir',
        'premature',
        'no_kk',
        'nik_ibu',
        'nama_ibu',
        'nik_ayah',
        'nama_ayah',
        'status_keaktifan',
        'tanggal_status',
        'alasan_status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}