<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal_posyandu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'judul',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Jadwal dianggap SELESAI setelah tanggal + waktu selesai terlewati (WIB).
     * Pada hari pelaksanaan, jadwal masih "mendatang" sampai waktu selesai lewat.
     */
    public static function sudahSelesai(array $jadwal): bool
    {
        $zona  = new \DateTimeZone('Asia/Jakarta');
        $akhir = new \DateTime($jadwal['tanggal'] . ' ' . $jadwal['waktu_selesai'], $zona);

        return $akhir < new \DateTime('now', $zona);
    }
}