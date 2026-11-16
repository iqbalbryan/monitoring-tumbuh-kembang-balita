<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\PengukuranModel;
use App\Models\JadwalModel;
use App\Models\ArtikelModel;
use App\Models\NotifikasiModel;

class OrangTua extends BaseController
{
    protected $anakModel;
    protected $pengukuranModel;
    protected $jadwalModel;
    protected $artikelModel;
    protected $notifikasiModel;

    public function __construct()
    {
        $this->anakModel        = new AnakModel();
        $this->pengukuranModel  = new PengukuranModel();
        $this->jadwalModel      = new JadwalModel();
        $this->artikelModel     = new ArtikelModel();
        $this->notifikasiModel  = new NotifikasiModel();
    }

    // Dashboard orang tua — Rapor Anak (halaman utama)
    public function dashboard()
    {
        $userId = session()->get('user_id');
        $userNik = session()->get('user_nik');
        if (!$userNik) {
            $userModel = new \App\Models\UserModel();
            $u = $userModel->find($userId);
            $userNik = $u['nik'] ?? null;
        }

        if (!$userNik) {
            return view('orangtua/dashboard_empty', [
                'title'        => 'Dashboard',
                'pageTitle'    => 'Dashboard Orang Tua',
                'pageSubtitle' => 'Selamat datang, ' . session()->get('user_nama') . '!',
                'activeMenu'   => 'dashboard',
                'notifCount'   => count($this->notifikasiModel->getUnread($userId)),
            ]);
        }

        // Akses berbasis NIK: tampilkan semua anak yang NIK ibu/ayah-nya = NIK user
        $anakList = $this->anakModel
            ->groupStart()
                ->where('nik_ibu', $userNik)
                ->orWhere('nik_ayah', $userNik)
            ->groupEnd()
            ->orderBy('nama', 'ASC')
            ->findAll();

        // Belum punya anak terdaftar
        if (empty($anakList)) {
            return view('orangtua/dashboard_empty', [
                'title'        => 'Dashboard',
                'pageTitle'    => 'Dashboard Orang Tua',
                'pageSubtitle' => 'Selamat datang, ' . session()->get('user_nama') . '!',
                'activeMenu'   => 'dashboard',
                'notifCount'   => count($this->notifikasiModel->getUnread($userId)),
            ]);
        }

        // Tentukan anak terpilih (?anak=ID, default anak pertama)
        $selectedId = $this->request->getGet('anak');
        $anak = null;
        if ($selectedId) {
            foreach ($anakList as $a) {
                if ((string) $a['id'] === (string) $selectedId) { $anak = $a; break; }
            }
        }
        if (!$anak) { $anak = $anakList[0]; }
        $id = $anak['id'];

        $pengukuran = $this->pengukuranModel
            ->where('anak_id', $id)
            ->orderBy('tanggal_ukur', 'ASC')
            ->findAll();

        $pengukuranTerakhir = $this->pengukuranModel
            ->where('anak_id', $id)
            ->orderBy('tanggal_ukur', 'DESC')
            ->first();

        $jadwal = $this->jadwalModel
            ->where('tanggal >=', date('Y-m-d'))
            ->orderBy('tanggal', 'ASC')
            ->findAll(3);

        $artikel = $this->artikelModel
            ->where('status', 'publikasi')
            ->orderBy('created_at', 'DESC')
            ->findAll(4);

        $notifikasi = $this->notifikasiModel->getUnread($userId);

        // Umur saat ini
        $tglLahir  = new \DateTime($anak['tanggal_lahir']);
        $umur      = $tglLahir->diff(new \DateTime());
        $umurBulan = ($umur->y * 12) + $umur->m;

        // Data grafik
        $grafikLabels = array_map(fn($p) => date('d/m/Y', strtotime($p['tanggal_ukur'])), $pengukuran);
        $grafikBB     = array_map(fn($p) => $p['berat_badan'], $pengukuran);
        $grafikTB     = array_map(fn($p) => $p['tinggi_badan'], $pengukuran);

        // Standar WHO
        $db = \Config\Database::connect();
        $jk = $anak['jenis_kelamin'] === 'Laki-laki' ? 'L' : 'P';
        $kmsData = $db->table('who_bb_u')->where('jenis_kelamin', $jk)->orderBy('umur_bulan', 'ASC')->get()->getResultArray();
        $kmsTBU  = $db->table('who_tb_u')->where('jenis_kelamin', $jk)->orderBy('umur_bulan', 'ASC')->get()->getResultArray();
        $kmsBBTB = $db->table('who_bb_tb')->where('jenis_kelamin', $jk)->orderBy('tinggi', 'ASC')->get()->getResultArray();

        return view('orangtua/dashboard', [
            'title'              => 'Dashboard - ' . $anak['nama'],
            'pageTitle'          => 'Dashboard Orang Tua',
            'pageSubtitle'       => 'Pantau tumbuh kembang ' . $anak['nama'],
            'activeMenu'         => 'dashboard',
            'notifCount'         => count($notifikasi),
            'isHome'             => true,
            'anakList'           => $anakList,
            'anak'               => $anak,
            'pengukuran'         => $pengukuran,
            'pengukuranTerakhir' => $pengukuranTerakhir,
            'jadwal'             => $jadwal,
            'artikel'            => $artikel,
            'notifikasi'         => $notifikasi,
            'umur'               => $umur,
            'umurBulan'          => $umurBulan,
            'grafikLabels'       => json_encode($grafikLabels),
            'grafikBB'           => json_encode($grafikBB),
            'grafikTB'           => json_encode($grafikTB),
            'kmsData'            => json_encode($kmsData),
            'kmsTBU'             => json_encode($kmsTBU),
            'kmsBBTB'            => json_encode($kmsBBTB),
        ]);
    }

    // Detail anak — kini menjadi halaman utama (dashboard dengan switcher)
    public function detailAnak($id)
    {
        return redirect()->to('/orangtua/dashboard?anak=' . $id);
    }

    // Form tambah anak
    public function tambahAnak()
    {
        $userId = session()->get('user_id');
        $data = [
            'title'        => 'Tambah Anak',
            'pageTitle'    => 'Tambah Anak',
            'pageSubtitle' => 'Hubungkan data anak ke akun Anda',
            'activeMenu'   => 'tambah',
            'notifCount'   => count($this->notifikasiModel->getUnread($userId)),
        ];
        return view('orangtua/tambah_anak', $data);
    }

    // Proses tambah anak
    public function simpanAnak()
    {
         $rules = [
            'nik_anak' => 'required|numeric|min_length[16]|max_length[20]',
        ];

        $messages = [
            'nik_anak' => [
                'required'   => 'NIK anak wajib diisi.',
                'numeric'    => 'NIK anak hanya boleh berisi angka.',
                'min_length' => 'NIK anak minimal 16 digit.',
                'max_length' => 'NIK anak maksimal 20 digit.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/orangtua/tambah-anak')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $userId  = session()->get('user_id');
        $nikAnak = $this->request->getPost('nik_anak');

        // Ambil data user yang sedang login
        $userModel = new \App\Models\UserModel();
        $user      = $userModel->find($userId);

        // Cari anak berdasarkan NIK
        $anak = $this->anakModel->where('nik_anak', $nikAnak)->first();

        if (!$anak) {
            return redirect()->to('/orangtua/tambah-anak')->with('error', 'NIK anak tidak ditemukan! Pastikan anak sudah terdaftar di Posyandu Nusa Indah.');
        }

        // Cek apakah NIK user cocok dengan NIK ibu atau NIK ayah di data anak
        if ($user['nik'] !== $anak['nik_ibu'] && $user['nik'] !== $anak['nik_ayah']) {
            return redirect()->to('/orangtua/tambah-anak')->with('error', 'Anda tidak berhak menambahkan anak ini! NIK Anda tidak terdaftar sebagai orang tua anak tersebut.');
        }

        // Cek apakah anak sudah terhubung ke akun lain
        if ($anak['user_id'] && $anak['user_id'] != $userId) {
            return redirect()->to('/orangtua/tambah-anak')->with('error', 'NIK anak ini sudah terhubung ke akun lain!');
        }

        // Cek apakah anak sudah terhubung ke akun ini
        if ($anak['user_id'] == $userId) {
            return redirect()->to('/orangtua/tambah-anak')->with('error', 'NIK anak ini sudah terdaftar di akun Anda!');
        }

        // Hubungkan anak ke akun orang tua
        $this->anakModel->update($anak['id'], ['user_id' => $userId]);

        // Kirim notifikasi
        $this->notifikasiModel->save([
            'user_id' => $userId,
            'judul'   => 'Anak Berhasil Ditambahkan',
            'pesan'   => 'Data anak ' . $anak['nama'] . ' berhasil terhubung ke akun Anda.',
            'tipe'    => 'info',
        ]);

        return redirect()->to('/orangtua/dashboard')->with('success', 'Anak berhasil ditambahkan!');
    }

    // Baca notifikasi
    public function bacaNotifikasi()
    {
        $userId = session()->get('user_id');
        $this->notifikasiModel->readAll($userId);
        return redirect()->back();
    }

    // Halaman notifikasi
    public function notifikasi()
    {
        $userId = session()->get('user_id');
        $data = [
            'title'        => 'Notifikasi',
            'pageTitle'    => 'Notifikasi',
            'pageSubtitle' => 'Semua pemberitahuan untuk Anda',
            'activeMenu'   => 'notifikasi',
            'notifikasi'   => $this->notifikasiModel->getAll($userId),
            'notifCount'   => count($this->notifikasiModel->getUnread($userId)),
        ];
        return view('orangtua/notifikasi', $data);
    }

    // Halaman artikel detail
    public function artikelDetail($id)
    {
        $artikel = $this->artikelModel->find($id);
        if (!$artikel) {
            return redirect()->to('/orangtua/dashboard');
        }
        $userId = session()->get('user_id');
        $data = [
            'title'        => $artikel['judul'],
            'pageTitle'    => 'Artikel & Edukasi',
            'pageSubtitle' => 'Bacaan kesehatan untuk keluarga',
            'activeMenu'   => 'artikel',
            'notifCount'   => count($this->notifikasiModel->getUnread($userId)),
            'artikel'      => $artikel,
        ];
        return view('orangtua/detail_artikel', $data);
    }
}
