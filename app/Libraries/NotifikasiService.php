<?php

namespace App\Libraries;

use App\Models\NotifikasiModel;
use App\Models\UserModel;
use App\Models\AnakModel;
use App\Models\PengukuranModel;
use App\Models\JadwalModel;

/**
 * Pusat pengiriman notifikasi (in-app + email) untuk Posyandu Nusa Indah.
 * Menggantikan fungsi sendEmail lama (approve/reject akun) dan dialihkan
 * untuk mendukung pemantauan tumbuh kembang anak.
 */
class NotifikasiService
{
    protected $notifModel;
    protected $userModel;
    protected $anakModel;
    protected $pengukuranModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->notifModel      = new NotifikasiModel();
        $this->userModel       = new UserModel();
        $this->anakModel       = new AnakModel();
        $this->pengukuranModel = new PengukuranModel();
        $this->jadwalModel     = new JadwalModel();

        helper('tanggal');
    }

    /**
     * Kirim 1 notifikasi ke seorang user: simpan in-app + (opsional) email.
     * $user boleh berupa ID user atau array data user.
     */
    public function kirim($user, $judul, $pesan, $tipe = 'info', $kirimEmail = true)
    {
        if (!is_array($user)) {
            $user = $this->userModel->find($user);
        }
        if (!$user) {
            return false;
        }

        // 1) Notifikasi in-app
        $this->notifModel->insert([
            'user_id'    => $user['id'],
            'judul'      => $judul,
            'pesan'      => $pesan,
            'tipe'       => $tipe,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // 2) Email (opsional)
        if ($kirimEmail && !empty($user['email'])) {
            $this->kirimEmail($user['email'], $user['nama'] ?? 'Orang Tua', $judul, $pesan);
        }

        return true;
    }

    /**
     * Kirim email HTML sederhana (logika dari sendEmail lama, digeneralisasi).
     */
    public function kirimEmail($toEmail, $toName, $judul, $pesan)
    {
        try {
            $email = \Config\Services::email();
            $email->setFrom('theoroseford8@gmail.com', 'Posyandu Nusa Indah');
            $email->setTo($toEmail);
            $email->setSubject($judul . ' - Posyandu Nusa Indah');
            $email->setMailType('html');
            $email->setMessage(
                "<div style='font-family:Arial,sans-serif;color:#333'>"
                . "<h3 style='color:#2563a8'>" . esc($judul) . "</h3>"
                . "<p>Halo <strong>" . esc($toName) . "</strong>,</p>"
                . "<p>" . nl2br(esc($pesan)) . "</p>"
                . "<br><a href='http://localhost:8080/login' style='background:#5BC8F5;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none'>Buka Aplikasi</a>"
                . "<br><br><p style='font-size:12px;color:#888'>Pesan otomatis dari Sistem Posyandu Nusa Indah.</p>"
                . "</div>"
            );
            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'Gagal kirim email notifikasi: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cari akun orang tua (aktif) berdasarkan data anak (nik_ibu / nik_ayah).
     */
    public function orangTuaDariAnak($anak)
    {
        $niks = array_filter([$anak['nik_ibu'] ?? null, $anak['nik_ayah'] ?? null]);
        if (empty($niks)) {
            return [];
        }
        return $this->userModel
            ->whereIn('nik', $niks)
            ->where('role', 'orangtua')
            ->where('status', 'aktif')
            ->findAll();
    }

    /**
     * Cegah notifikasi ganda: true bila belum ada notif berjudul + pesan sama untuk user ini.
     */
    protected function belumPernah($userId, $judul, $pesan)
    {
        $ada = $this->notifModel
            ->where('user_id', $userId)
            ->where('judul', $judul)
            ->where('pesan', $pesan)
            ->first();
        return empty($ada);
    }

    // =====================================================================
    // 1) ALERT PERTUMBUHAN BERISIKO (dipicu setelah input pengukuran)
    // =====================================================================
    public function alertPertumbuhan($anak, $status, $tanggalUkur)
    {
        if (empty($anak)) {
            return 0;
        }

        $masalah = [];
        $bbu = $status['status_gizi'] ?? '';
        if (in_array($bbu, ['BB Kurang', 'BB Sangat Kurang'])) {
            $masalah[] = 'Berat badan menurut umur: ' . $bbu;
        }
        $tbu = $status['status_tb'] ?? '';
        if (in_array($tbu, ['Pendek', 'Sangat Pendek'])) {
            $masalah[] = 'Tinggi badan menurut umur: ' . $tbu . ' (indikasi stunting)';
        }
        $bbtb = $status['status_gizi_bbtb'] ?? '';
        if (in_array($bbtb, ['Gizi Kurang', 'Gizi Buruk'])) {
            $masalah[] = 'Status gizi (BB/TB): ' . $bbtb;
        }

        if (empty($masalah)) {
            return 0;
        }

        $ortu = $this->orangTuaDariAnak($anak);
        if (empty($ortu)) {
            return 0;
        }

        $tgl   = date('d-m-Y', strtotime($tanggalUkur));
        $judul = 'Perhatian: Hasil Pemeriksaan ' . $anak['nama'];
        $pesan = 'Hasil penimbangan tanggal ' . $tgl . ' untuk ananda ' . $anak['nama'] . " perlu perhatian:\n- "
            . implode("\n- ", $masalah)
            . "\n\nMohon konsultasikan dengan kader/petugas posyandu untuk penanganan lebih lanjut.";

        $count = 0;
        foreach ($ortu as $u) {
            $this->kirim($u, $judul, $pesan, 'status_gizi', true);
            $count++;
        }
        return $count;
    }

    // =====================================================================
    // 2) NOTIFIKASI TERJADWAL
    // =====================================================================
    public function jalankanTerjadwal($force = false)
    {
        return [
            'jadwal'          => $this->notifJadwalMendatang($force),
            'belum_ditimbang' => $this->notifBelumDitimbang($force),
            'akan_lulus'      => $this->notifAkanLulus(),
        ];
    }

    /** Pengingat jadwal posyandu (H-2). $force = kirim jadwal 14 hari ke depan. */
    public function notifJadwalMendatang($force = false)
    {
        $today = date('Y-m-d');
        if ($force) {
            $jadwalList = $this->jadwalModel
                ->where('tanggal >=', $today)
                ->where('tanggal <=', date('Y-m-d', strtotime('+14 days')))
                ->orderBy('tanggal', 'ASC')
                ->findAll();
        } else {
            $target     = date('Y-m-d', strtotime('+2 days'));
            $jadwalList = $this->jadwalModel->where('tanggal', $target)->findAll();
        }
        if (empty($jadwalList)) {
            return 0;
        }

        $ortu = $this->userModel->where('role', 'orangtua')->where('status', 'aktif')->findAll();
        if (empty($ortu)) {
            return 0;
        }

        $count = 0;
        foreach ($jadwalList as $j) {
            $tgl   = tglIndo($j['tanggal']);
            $jam   = !empty($j['waktu_mulai']) ? date('H:i', strtotime($j['waktu_mulai'])) : '';
            $judul = 'Pengingat Jadwal Posyandu';
            $pesan = 'Jangan lupa hadir di Posyandu Nusa Indah pada ' . $tgl
                . ($jam ? ' pukul ' . $jam . ' WIB' : '')
                . (!empty($j['lokasi']) ? ' di ' . $j['lokasi'] : '')
                . '. Agenda: ' . (!empty($j['judul']) ? $j['judul'] : 'Penimbangan & pemantauan tumbuh kembang') . '.';
            foreach ($ortu as $u) {
                if (!$this->belumPernah($u['id'], $judul, $pesan)) {
                    continue;
                }
                $this->kirim($u, $judul, $pesan, 'jadwal', true);
                $count++;
            }
        }
        return $count;
    }

    /** Pengingat anak belum ditimbang bulan ini (default hanya bila tanggal >= 25). */
    public function notifBelumDitimbang($force = false)
    {
        if (!$force && (int) date('j') < 25) {
            return 0;
        }

        $bulan     = (int) date('n');
        $tahun     = (int) date('Y');
        $namaBulan = blnTahunIndo();

        $anakAktif = $this->anakModel
            ->where('status_keaktifan', 'Aktif')
            ->findAll();

        $count = 0;
        foreach ($anakAktif as $anak) {
            $sudah = $this->pengukuranModel
                ->where('anak_id', $anak['id'])
                ->where('MONTH(tanggal_ukur)', $bulan)
                ->where('YEAR(tanggal_ukur)', $tahun)
                ->first();
            if ($sudah) {
                continue;
            }

            $ortu = $this->orangTuaDariAnak($anak);
            if (empty($ortu)) {
                continue;
            }

            $judul = 'Pengingat Penimbangan Bulanan';
            $pesan = 'Ananda ' . $anak['nama'] . ' belum ditimbang pada bulan ' . $namaBulan . '. '
                . 'Mohon bawa ke posyandu agar tumbuh kembangnya tetap terpantau.';
            foreach ($ortu as $u) {
                if (!$this->belumPernah($u['id'], $judul, $pesan)) {
                    continue;
                }
                $this->kirim($u, $judul, $pesan, 'pengukuran', true);
                $count++;
            }
        }
        return $count;
    }

    /** Pengingat anak akan lulus (usia 59-60 bulan). */
    public function notifAkanLulus()
    {
        helper('zscore');
        $namaBulan = blnTahunIndo();

        $anakAktif = $this->anakModel
            ->where('status_verifikasi', 'aktif')
            ->where('status_keaktifan', 'Aktif')
            ->findAll();

        $count = 0;
        foreach ($anakAktif as $anak) {
            if (empty($anak['tanggal_lahir'])) {
                continue;
            }
            $umur = hitungUmurBulan($anak['tanggal_lahir'], date('Y-m-d'));
            if ($umur < 59 || $umur > 60) {
                continue;
            }

            $ortu = $this->orangTuaDariAnak($anak);
            if (empty($ortu)) {
                continue;
            }

            $judul = 'Ananda Akan Lulus Posyandu';
            $pesan = 'Ananda ' . $anak['nama'] . ' kini berusia ' . $umur . ' bulan dan akan segera lulus '
                . 'dari pemantauan balita (usia 60 bulan). Pastikan penimbangan terakhir dilakukan dan '
                . 'tanyakan ke kader mengenai tahap selanjutnya. (' . $namaBulan . ')';
            foreach ($ortu as $u) {
                if (!$this->belumPernah($u['id'], $judul, $pesan)) {
                    continue;
                }
                $this->kirim($u, $judul, $pesan, 'info', true);
                $count++;
            }
        }
        return $count;
    }
}
