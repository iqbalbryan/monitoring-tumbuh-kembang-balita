<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

    }

    // Dashboard admin
    public function dashboard()
    {
        $userModel        = new \App\Models\UserModel();
        $anakModel        = new \App\Models\AnakModel();
        $pengukuranModel  = new \App\Models\PengukuranModel();
        $jadwalModel      = new \App\Models\JadwalModel();
        $db               = \Config\Database::connect();

        // Filter periode (bulan & tahun) untuk data snapshot dashboard
        $lastRow   = $db->query("SELECT YEAR(tanggal_ukur) th, MONTH(tanggal_ukur) bl FROM pengukuran ORDER BY tanggal_ukur DESC LIMIT 1")->getRowArray();
        $defBulan  = $lastRow ? (int) $lastRow['bl'] : (int) date('n');
        $defTahun  = $lastRow ? (int) $lastRow['th'] : (int) date('Y');
        $bulan     = (int) ($this->request->getGet('bulan') ?: $defBulan);
        $tahun     = (int) ($this->request->getGet('tahun') ?: $defTahun);
        $tahunRows = $db->query("SELECT DISTINCT YEAR(tanggal_ukur) th FROM pengukuran ORDER BY th DESC")->getResultArray();
        $tahunList = array_column($tahunRows, 'th');
        if (empty($tahunList)) { $tahunList = [(int) date('Y')]; }
        if (!in_array($tahun, $tahunList)) { array_unshift($tahunList, $tahun); }

        // Statistik user
        $total_user    = $userModel->where('role', 'orangtua')->countAllResults();
        $total_aktif   = $userModel->where('status', 'aktif')->countAllResults();

        // Statistik anak
        // Total anak aktif = aktif yang belum kandidat lulus (sama dengan tab Aktif di Data Anak)
        $total_anak = $anakModel->where('status_keaktifan', 'Aktif')
            ->groupStart()
                ->where('tanggal_lahir IS NULL', null, false)
                ->orWhere('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60', null, false)
            ->groupEnd()
            ->countAllResults();

        // Anak aktif yang sudah >= 60 bulan (kandidat lulus)
        $kandidat_lulus = $db->query("
            SELECT COUNT(*) as total FROM anak
            WHERE status_keaktifan = 'Aktif'
            AND TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) >= 60
        ")->getRow()->total;

        // Anak belum pernah diukur
        $belum_diukur = $db->query("
            SELECT COUNT(*) as total FROM anak 
            WHERE anak.status_keaktifan = 'Aktif' AND NOT EXISTS (
                SELECT 1 FROM pengukuran p
                WHERE p.anak_id = anak.id
                AND YEAR(p.tanggal_ukur) = $tahun AND MONTH(p.tanggal_ukur) = $bulan
            )
        ")->getRow()->total;

        // Statistik status gizi (pengukuran terakhir tiap anak)
        $statusGizi = $db->query("
            SELECT
                CASE
                    WHEN p.z_score_bb_u < -3 THEN 'BB Sangat Kurang'
                    WHEN p.z_score_bb_u < -2 THEN 'BB Kurang'
                    WHEN p.z_score_bb_u <= 1 THEN 'BB Normal'
                    ELSE 'Risiko BB Lebih'
                END AS status_bb,
                COUNT(*) as total
            FROM pengukuran p
            INNER JOIN (
                SELECT anak_id, MAX(tanggal_ukur) as max_tgl
                FROM pengukuran
                WHERE YEAR(tanggal_ukur) = $tahun AND MONTH(tanggal_ukur) = $bulan
                GROUP BY anak_id
            ) latest ON p.anak_id = latest.anak_id AND p.tanggal_ukur = latest.max_tgl
            WHERE p.z_score_bb_u IS NOT NULL
            GROUP BY status_bb
        ")->getResultArray();

        $giziStats = [
            'BB Normal'        => 0,
            'BB Kurang'        => 0,
            'BB Sangat Kurang' => 0,
            'Risiko BB Lebih'  => 0,
        ];
        foreach ($statusGizi as $g) {
            if (isset($giziStats[$g['status_bb']])) {
                $giziStats[$g['status_bb']] = $g['total'];
            }
        }

        // Statistik pertumbuhan PB/U atau TB/U (stunting) - pengukuran terakhir tiap anak
        $statusTBU = $db->query("
            SELECT
                CASE
                    WHEN p.z_score_tb_u < -3 THEN 'Sangat Pendek'
                    WHEN p.z_score_tb_u < -2 THEN 'Pendek'
                    WHEN p.z_score_tb_u <= 2 THEN 'Normal'
                    ELSE 'Tinggi'
                END AS status_tb,
                COUNT(*) as total
            FROM pengukuran p
            INNER JOIN (
                SELECT anak_id, MAX(tanggal_ukur) as max_tgl
                FROM pengukuran
                WHERE YEAR(tanggal_ukur) = $tahun AND MONTH(tanggal_ukur) = $bulan
                GROUP BY anak_id
            ) latest ON p.anak_id = latest.anak_id AND p.tanggal_ukur = latest.max_tgl
            WHERE p.z_score_tb_u IS NOT NULL
            GROUP BY status_tb
        ")->getResultArray();

        $tbuStats = [
            'Normal'        => 0,
            'Pendek'        => 0,
            'Sangat Pendek' => 0,
            'Tinggi'        => 0,
        ];
        foreach ($statusTBU as $t) {
            if (isset($tbuStats[$t['status_tb']])) {
                $tbuStats[$t['status_tb']] = $t['total'];
            }
        }
        $total_stunting = $tbuStats['Pendek'] + $tbuStats['Sangat Pendek'];

        // Statistik status gizi BB/TB (wasting) - pengukuran terakhir tiap anak
        helper('zscore');
        $latestBBTB = $db->query("
            SELECT a.jenis_kelamin, p.berat_badan, p.tinggi_badan, p.z_score_bb_tb
            FROM pengukuran p
            INNER JOIN anak a ON a.id = p.anak_id
            INNER JOIN (
                SELECT anak_id, MAX(tanggal_ukur) as max_tgl
                FROM pengukuran
                WHERE YEAR(tanggal_ukur) = $tahun AND MONTH(tanggal_ukur) = $bulan
                GROUP BY anak_id
            ) latest ON p.anak_id = latest.anak_id AND p.tanggal_ukur = latest.max_tgl
        ")->getResultArray();

        $bbtbStats = [
            'Gizi Buruk'          => 0,
            'Gizi Kurang'         => 0,
            'Gizi Baik'           => 0,
            'Berisiko Gizi Lebih' => 0,
            'Gizi Lebih'          => 0,
            'Obesitas'            => 0,
        ];
        foreach ($latestBBTB as $row) {
            $z = $row['z_score_bb_tb'];
            if (($z === null || $z === '') && !empty($row['berat_badan']) && !empty($row['tinggi_badan'])) {
                $z = hitungZScoreBBTB((float) $row['berat_badan'], (float) $row['tinggi_badan'], $row['jenis_kelamin'], $db);
            }
            if ($z === null || $z === '') continue;
            $st = statusGiziBBTB((float) $z);
            if (isset($bbtbStats[$st])) $bbtbStats[$st]++;
        }
        $total_wasting = $bbtbStats['Gizi Buruk'] + $bbtbStats['Gizi Kurang'];

        // Total pengukuran bulan ini
        $total_pengukuran_bulan = $db->query("
            SELECT COUNT(*) as total FROM pengukuran
            WHERE MONTH(tanggal_ukur) = $bulan
            AND YEAR(tanggal_ukur) = $tahun
        ")->getRow()->total;

        // Grafik pengukuran per bulan untuk tahun terpilih (hanya bulan yang memiliki data)
        $grafikRaw = $db->query("
            SELECT 
                DATE_FORMAT(tanggal_ukur, '%Y-%m') as bulan_sort,
                COUNT(*) as total
            FROM pengukuran
            WHERE YEAR(tanggal_ukur) = $tahun
            GROUP BY bulan_sort
            ORDER BY bulan_sort ASC
        ")->getResultArray();
        $grafikPengukuran = [];
        foreach ($grafikRaw as $r) {
            $ts = strtotime($r['bulan_sort'] . '-01');
            $grafikPengukuran[] = [
                'bulan'      => blnSingkatIndo($ts),
                'bulan_sort' => $r['bulan_sort'],
                'total'      => (int) $r['total'],
            ];
        }

        // Anak dengan gizi buruk/kurang (butuh perhatian)
        $anakPerhatian = $db->query("
            SELECT a.id, a.nama, a.nik_anak, a.jenis_kelamin, a.tanggal_lahir,
                p.berat_badan, p.tinggi_badan, p.status_gizi, p.z_score_tb_u, p.z_score_bb_tb, p.tanggal_ukur
            FROM anak a
            INNER JOIN pengukuran p ON a.id = p.anak_id
            INNER JOIN (
                SELECT anak_id, MAX(tanggal_ukur) as max_tgl
                FROM pengukuran
                WHERE YEAR(tanggal_ukur) = $tahun AND MONTH(tanggal_ukur) = $bulan
                GROUP BY anak_id
            ) latest ON p.anak_id = latest.anak_id AND p.tanggal_ukur = latest.max_tgl
            WHERE p.status_gizi IN ('BB Sangat Kurang', 'BB Kurang')
               OR p.z_score_tb_u < -2
               OR p.z_score_bb_tb < -2
            ORDER BY a.nama ASC
        ")->getResultArray();

        // Jadwal posyandu berikutnya
        $jadwalBerikutnya = $jadwalModel
            ->where('tanggal >=', date('Y-m-d'))
            ->orderBy('tanggal', 'ASC')
            ->first();

        $data = [
            'title'                  => 'Dashboard Admin',
            'bulan'                  => $bulan,
            'tahun'                  => $tahun,
            'tahunList'              => $tahunList,
            'total_user'             => $total_user,
            'total_aktif'            => $total_aktif,
            'total_anak'             => $total_anak,
            'belum_diukur'           => $belum_diukur,
            'kandidat_lulus'         => $kandidat_lulus,
            'giziStats'              => $giziStats,
            'tbuStats'               => $tbuStats,
            'total_stunting'         => $total_stunting,
            'bbtbStats'              => $bbtbStats,
            'total_wasting'          => $total_wasting,
            'total_pengukuran_bulan' => $total_pengukuran_bulan,
            'grafikPengukuran'       => json_encode($grafikPengukuran),
            'anakPerhatian'          => $anakPerhatian,
            'jadwalBerikutnya'       => $jadwalBerikutnya,
        ];

        return view('admin/dashboard', $data);
    }

    // Lihat semua user (dengan pencarian)
    public function users()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = $this->userModel->where('role', 'orangtua');
        if ($q !== '') {
            $builder->groupStart()
                    ->like('nama', $q)
                    ->orLike('nik', $q)
                    ->orLike('email', $q)
                    ->groupEnd();
        }

        $data = [
            'title' => 'Manajemen User',
            'q'     => $q,
            'users' => $builder->orderBy('created_at', 'DESC')->findAll(),
        ];
        return view('admin/users', $data);
    }

    // Detail user + daftar anak terkait
    public function userDetail($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user || ($user['role'] ?? '') !== 'orangtua') {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan!');
        }

        $anakModel = new \App\Models\AnakModel();
        $anak = $anakModel->groupStart()
                    ->where('nik_ibu', $user['nik'])
                    ->orWhere('nik_ayah', $user['nik'])
                ->groupEnd()
                ->orderBy('nama', 'ASC')
                ->findAll();

        return view('admin/user_detail', [
            'title' => 'Detail User',
            'user'  => $user,
            'anak'  => $anak,
        ]);
    }

    // Aktifkan / nonaktifkan akun user
    public function toggleStatus($id = null)
    {
        $user = $this->userModel->find($id);
        if (!$user || ($user['role'] ?? '') !== 'orangtua') {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan!');
        }

        $statusBaru = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        $this->userModel->update($id, ['status' => $statusBaru]);

        $pesan = ($statusBaru === 'aktif')
            ? 'Akun ' . $user['nama'] . ' berhasil diaktifkan.'
            : 'Akun ' . $user['nama'] . ' berhasil dinonaktifkan.';
        return redirect()->back()->with('success', $pesan);
    }

    // Jalankan notifikasi terjadwal secara manual (dipicu tombol admin)
    public function jalankanNotifikasi()
    {
        $svc   = new \App\Libraries\NotifikasiService();
        $hasil = $svc->jalankanTerjadwal(true);
        $total = array_sum($hasil);

        $pesan = 'Notifikasi terkirim - ' . $hasil['jadwal'] . ' jadwal, ' . $hasil['belum_ditimbang'] . ' pengingat timbang, ' . $hasil['akan_lulus'] . ' akan lulus (total ' . $total . ').';
        return redirect()->to('/admin/dashboard')->with('success', $pesan);
    }
}
