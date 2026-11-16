<?php

namespace App\Models;

use CodeIgniter\Model;

class PengukuranModel extends Model
{
    protected $table            = 'pengukuran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'anak_id',
        'tanggal_ukur',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lila',
        'ntob',
        'status_gizi',
        'z_score_bb_u',
        'z_score_tb_u',
        'z_score_bb_tb',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Tentukan status N/T/O/B untuk satu pengukuran.
     *
     * Acuan: penimbangan terakhir SEBELUM bulan pengukuran ini.
     *  - B : belum ada penimbangan sebelumnya (balita baru)
     *  - O : penimbangan terakhir lebih dari 1 bulan kalender yang lalu
     *        (tidak ditimbang bulan lalu)
     *  - N : ditimbang bulan lalu & berat badan sekarang lebih besar (naik)
     *  - T : ditimbang bulan lalu & berat badan sama/lebih kecil (tidak naik)
     *
     * @param array $row        pengukuran yang dinilai (butuh tanggal_ukur & berat_badan)
     * @param array $sebelumnya pengukuran anak yang sama, urut tanggal ascending
     */
    public static function tentukanNTOB(array $row, array $sebelumnya): string
    {
        $tsRow     = strtotime($row['tanggal_ukur']);
        $awalBulan = date('Y-m-01', $tsRow);

        $ref = null;
        foreach ($sebelumnya as $s) {
            if ($s['tanggal_ukur'] < $awalBulan) {
                $ref = $s; // karena urut ascending, yang terakhir = paling baru
            }
        }

        if ($ref === null) {
            return 'B';
        }

        $tsRef   = strtotime($ref['tanggal_ukur']);
        $selisih = ((int) date('Y', $tsRow) * 12 + (int) date('n', $tsRow))
                 - ((int) date('Y', $tsRef) * 12 + (int) date('n', $tsRef));

        if ($selisih > 1) {
            return 'O';
        }

        return ((float) $row['berat_badan'] > (float) $ref['berat_badan']) ? 'N' : 'T';
    }

    /**
     * Hitung & simpan N/T/O/B untuk pengukuran anak mulai dari tanggal tertentu.
     * Dipanggil setelah menyimpan pengukuran baru. Pengukuran yang lebih baru
     * dari tanggal tersebut ikut dihitung ulang (jaga-jaga bila input mundur),
     * sedangkan data lama sebelum tanggal itu tidak diubah.
     */
    public function sinkronkanNTOB(int $anakId, string $mulaiTanggal): void
    {
        $rows = $this->where('anak_id', $anakId)
            ->orderBy('tanggal_ukur', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($rows as $i => $row) {
            if ($row['tanggal_ukur'] < $mulaiTanggal) {
                continue;
            }

            $ntob = self::tentukanNTOB($row, array_slice($rows, 0, $i));

            if (($row['ntob'] ?? null) !== $ntob) {
                // pakai builder agar updated_at tidak ikut berubah
                $this->builder()->where('id', $row['id'])->update(['ntob' => $ntob]);
            }
        }
    }
}