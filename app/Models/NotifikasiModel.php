<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
        'created_at',
    ];

    protected $useTimestamps = false;

    // Ambil notifikasi belum dibaca
    public function getUnread($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // Ambil semua notifikasi
    public function getAll($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // Tandai semua sudah dibaca
    public function readAll($userId)
    {
        return $this->where('user_id', $userId)
                    ->set('is_read', 1)
                    ->update();
    }
}