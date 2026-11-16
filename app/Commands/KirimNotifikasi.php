<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotifikasiService;

/**
 * Spark command untuk notifikasi terjadwal.
 * Jalankan harian via cron / Windows Task Scheduler:
 *   php spark notifikasi:kirim
 * Tambahkan --force untuk mengabaikan filter tanggal (uji coba / manual).
 */
class KirimNotifikasi extends BaseCommand
{
    protected $group       = 'Posyandu';
    protected $name        = 'notifikasi:kirim';
    protected $description = 'Kirim notifikasi terjadwal: jadwal posyandu H-2, anak belum ditimbang, dan anak akan lulus.';
    protected $usage       = 'notifikasi:kirim [--force]';
    protected $options     = ['--force' => 'Abaikan filter tanggal dan kirim semua yang relevan sekarang.'];

    public function run(array $params)
    {
        $force = (bool) CLI::getOption('force');

        $svc   = new NotifikasiService();
        $hasil = $svc->jalankanTerjadwal($force);

        CLI::write('=== Notifikasi Posyandu Nusa Indah ===', 'yellow');
        CLI::write('Pengingat jadwal posyandu : ' . $hasil['jadwal'], 'green');
        CLI::write('Pengingat belum ditimbang : ' . $hasil['belum_ditimbang'], 'green');
        CLI::write('Pengingat akan lulus      : ' . $hasil['akan_lulus'], 'green');
        CLI::write('Total notifikasi terkirim : ' . array_sum($hasil), 'light_green');
    }
}
