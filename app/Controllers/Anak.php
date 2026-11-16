<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\PengukuranModel;
use App\Libraries\AkunOrangTua;

class Anak extends BaseController
{
    protected $anakModel;
    protected $pengukuranModel;

    public function __construct()
    {
        $this->anakModel       = new AnakModel();
        $this->pengukuranModel = new PengukuranModel();
    }

    // Lihat semua data anak
    public function index()
    {
        $keyword = $this->request->getGet('keyword') ?? '';
        $jk      = $this->request->getGet('jk') ?? '';
        $status  = $this->request->getGet('status') ?? 'aktif';

        $builder = $this->anakModel;

        if ($keyword) {
            $builder = $builder->groupStart()
                ->like('nama', $keyword)
                ->orLike('nik_anak', $keyword)
                ->orLike('nama_ibu', $keyword)
                ->orLike('nama_ayah', $keyword)
                ->groupEnd();
        }

        if ($jk) {
            $builder = $builder->where('jenis_kelamin', $jk);
        }

        if ($status === 'arsip') {
            $builder = $builder->where('status_keaktifan !=', 'Aktif');
        } elseif ($status === 'kandidat') {
            // Kandidat lulus: masih Aktif dan umur >= 60 bulan (sama dengan dashboard & laporan)
            $builder = $builder->where('status_keaktifan', 'Aktif')
                ->where('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) >= 60', null, false);
        } else {
            // Tab Aktif: anak aktif yang BELUM kandidat lulus (umur < 60 bulan)
            $status  = 'aktif';
            $builder = $builder->where('status_keaktifan', 'Aktif')
                ->groupStart()
                    ->where('tanggal_lahir IS NULL', null, false)
                    ->orWhere('TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) < 60', null, false)
                ->groupEnd();
        }

        $data = [
            'title'   => 'Data Anak',
            'anak'    => $builder->findAll(),
            'keyword' => $keyword,
            'jk'      => $jk,
            'status'  => $status,
        ];
        return view('anak/index', $data);
    }

    // Form tambah anak
    public function create()
    {
        $data = ['title' => 'Tambah Data Anak'];
        return view('anak/create', $data);
    }

    // Simpan data anak baru
    public function store()
    {
        $rules = [
            'nik_anak'             => 'required|numeric|min_length[16]|max_length[20]|is_unique[anak.nik_anak]',
            'nama'                 => 'required|min_length[2]|max_length[100]',
            'jenis_kelamin'        => 'required|in_list[Laki-laki,Perempuan]',
            'tanggal_lahir'        => 'required|valid_date[Y-m-d]',
            'berat_badan_lahir'    => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[10]',
            'panjang_badan_lahir'  => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[70]',
            'lingkar_kepala_lahir' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[50]',
            'premature'            => 'permit_empty|in_list[Premature,Mature]',
            'no_kk'                => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nik_ibu'              => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nik_ayah'             => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nama_ibu'             => 'permit_empty|max_length[100]',
            'nama_ayah'            => 'permit_empty|max_length[100]',
        ];

        $messages = [
            'nik_anak' => [
                'required'   => 'NIK anak wajib diisi.',
                'numeric'    => 'NIK anak hanya boleh berisi angka.',
                'min_length' => 'NIK anak minimal 16 digit.',
                'max_length' => 'NIK anak maksimal 20 digit.',
                'is_unique'  => 'NIK anak sudah terdaftar.',
            ],
            'nama' => [
                'required'   => 'Nama anak wajib diisi.',
                'min_length' => 'Nama anak minimal 2 karakter.',
                'max_length' => 'Nama anak maksimal 100 karakter.',
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin wajib dipilih.',
                'in_list'  => 'Jenis kelamin tidak valid.',
            ],
            'tanggal_lahir' => [
                'required'   => 'Tanggal lahir wajib diisi.',
                'valid_date' => 'Format tanggal lahir tidak valid.',
            ],
            'berat_badan_lahir' => [
                'decimal'               => 'Berat badan lahir harus berupa angka.',
                'greater_than_equal_to' => 'Berat badan lahir tidak boleh negatif.',
                'less_than'             => 'Berat badan lahir tidak masuk akal.',
            ],
            'panjang_badan_lahir' => [
                'decimal'               => 'Panjang badan lahir harus berupa angka.',
                'greater_than_equal_to' => 'Panjang badan lahir tidak boleh negatif.',
                'less_than'             => 'Panjang badan lahir tidak masuk akal.',
            ],
            'lingkar_kepala_lahir' => [
                'decimal'               => 'Lingkar kepala lahir harus berupa angka.',
                'greater_than_equal_to' => 'Lingkar kepala lahir tidak boleh negatif.',
                'less_than'             => 'Lingkar kepala lahir tidak masuk akal.',
            ],
            'premature' => [
                'in_list' => 'Status kelahiran tidak valid.',
            ],
            'no_kk' => [
                'numeric'    => 'Nomor KK hanya boleh berisi angka.',
                'min_length' => 'Nomor KK minimal 16 digit.',
                'max_length' => 'Nomor KK maksimal 20 digit.',
            ],
            'nik_ibu' => [
                'numeric'    => 'NIK ibu hanya boleh berisi angka.',
                'min_length' => 'NIK ibu minimal 16 digit.',
                'max_length' => 'NIK ibu maksimal 20 digit.',
            ],
            'nik_ayah' => [
                'numeric'    => 'NIK ayah hanya boleh berisi angka.',
                'min_length' => 'NIK ayah minimal 16 digit.',
                'max_length' => 'NIK ayah maksimal 20 digit.',
            ],
            'nama_ibu' => [
                'max_length' => 'Nama ibu maksimal 100 karakter.',
            ],
            'nama_ayah' => [
                'max_length' => 'Nama ayah maksimal 100 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/anak/create')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->anakModel->save([
            'nik_anak'             => trim($this->request->getPost('nik_anak')),
            'nama'                 => trim($this->request->getPost('nama')),
            'jenis_kelamin'        => $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir'        => $this->request->getPost('tanggal_lahir'),
            'berat_badan_lahir'    => $this->request->getPost('berat_badan_lahir') ?: null,
            'panjang_badan_lahir'  => $this->request->getPost('panjang_badan_lahir') ?: null,
            'lingkar_kepala_lahir' => $this->request->getPost('lingkar_kepala_lahir') ?: null,
            'premature'            => $this->request->getPost('premature') ?: 'Mature',
            'no_kk'                => trim((string) $this->request->getPost('no_kk')) ?: null,
            'nik_ibu'              => trim((string) $this->request->getPost('nik_ibu')) ?: null,
            'nama_ibu'             => trim((string) $this->request->getPost('nama_ibu')) ?: null,
            'nik_ayah'             => trim((string) $this->request->getPost('nik_ayah')) ?: null,
            'nama_ayah'            => trim((string) $this->request->getPost('nama_ayah')) ?: null,
            'status_keaktifan'     => 'Aktif',
        ]);

        // Buat akun orang tua otomatis (username = NIK ibu/ayah)
        $akun  = (new AkunOrangTua())->pastikanAkun((int) $this->anakModel->getInsertID());
        $pesan = 'Data anak berhasil ditambahkan!';
        if ($akun['baru']) {
            $pesan .= ' Akun orang tua dibuat otomatis (username & password awal = NIK).';
        }

        return redirect()->to('/anak')->with('success', $pesan);
    }

    // Form edit anak
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Anak',
            'anak'  => $this->anakModel->find($id)
        ];
        return view('anak/edit', $data);
    }

    // Update data anak
    public function update($id)
    {
        $anak = $this->anakModel->find($id);

        if (!$anak) {
            return redirect()->to('/anak')->with('error', 'Data anak tidak ditemukan.');
        }

        $rules = [
            'nik_anak' => 'required|numeric|min_length[16]|max_length[20]',
            'nama'                 => 'required|min_length[2]|max_length[100]',
            'jenis_kelamin'        => 'required|in_list[Laki-laki,Perempuan]',
            'tanggal_lahir'        => 'required|valid_date[Y-m-d]',
            'berat_badan_lahir'    => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[10]',
            'panjang_badan_lahir'  => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[70]',
            'lingkar_kepala_lahir' => 'permit_empty|decimal|greater_than_equal_to[0]|less_than[50]',
            'premature'            => 'permit_empty|in_list[Premature,Mature]',
            'no_kk'                => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nik_ibu'              => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nik_ayah'             => 'permit_empty|numeric|min_length[16]|max_length[20]',
            'nama_ibu'             => 'permit_empty|max_length[100]',
            'nama_ayah'            => 'permit_empty|max_length[100]',
        ];

        $messages = [
            'nik_anak' => [
                'required'   => 'NIK anak wajib diisi.',
                'numeric'    => 'NIK anak hanya boleh berisi angka.',
                'min_length' => 'NIK anak minimal 16 digit.',
                'max_length' => 'NIK anak maksimal 20 digit.',
                'is_unique'  => 'NIK anak sudah terdaftar.',
            ],
            'nama' => [
                'required'   => 'Nama anak wajib diisi.',
                'min_length' => 'Nama anak minimal 2 karakter.',
                'max_length' => 'Nama anak maksimal 100 karakter.',
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin wajib dipilih.',
                'in_list'  => 'Jenis kelamin tidak valid.',
            ],
            'tanggal_lahir' => [
                'required'   => 'Tanggal lahir wajib diisi.',
                'valid_date' => 'Format tanggal lahir tidak valid.',
            ],
            'berat_badan_lahir' => [
                'decimal'               => 'Berat badan lahir harus berupa angka.',
                'greater_than_equal_to' => 'Berat badan lahir tidak boleh negatif.',
                'less_than'             => 'Berat badan lahir tidak masuk akal.',
            ],
            'panjang_badan_lahir' => [
                'decimal'               => 'Panjang badan lahir harus berupa angka.',
                'greater_than_equal_to' => 'Panjang badan lahir tidak boleh negatif.',
                'less_than'             => 'Panjang badan lahir tidak masuk akal.',
            ],
            'lingkar_kepala_lahir' => [
                'decimal'               => 'Lingkar kepala lahir harus berupa angka.',
                'greater_than_equal_to' => 'Lingkar kepala lahir tidak boleh negatif.',
                'less_than'             => 'Lingkar kepala lahir tidak masuk akal.',
            ],
            'premature' => [
                'in_list' => 'Status kelahiran tidak valid.',
            ],
            'no_kk' => [
                'numeric'    => 'Nomor KK hanya boleh berisi angka.',
                'min_length' => 'Nomor KK minimal 16 digit.',
                'max_length' => 'Nomor KK maksimal 20 digit.',
            ],
            'nik_ibu' => [
                'numeric'    => 'NIK ibu hanya boleh berisi angka.',
                'min_length' => 'NIK ibu minimal 16 digit.',
                'max_length' => 'NIK ibu maksimal 20 digit.',
            ],
            'nik_ayah' => [
                'numeric'    => 'NIK ayah hanya boleh berisi angka.',
                'min_length' => 'NIK ayah minimal 16 digit.',
                'max_length' => 'NIK ayah maksimal 20 digit.',
            ],
            'nama_ibu' => [
                'max_length' => 'Nama ibu maksimal 100 karakter.',
            ],
            'nama_ayah' => [
                'max_length' => 'Nama ayah maksimal 100 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/anak/edit/' . $id)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $nikAnak = trim($this->request->getPost('nik_anak'));

        $existingNik = $this->anakModel
            ->where('nik_anak', $nikAnak)
            ->where('id !=', $id)
            ->first();

        if ($existingNik) {
            return redirect()->to('/anak/edit/' . $id)
                ->withInput()
                ->with('error', 'NIK anak sudah terdaftar.');
        }

        $data = [
            'nik_anak'             => trim($this->request->getPost('nik_anak')),
            'nama'                 => trim($this->request->getPost('nama')),
            'jenis_kelamin'        => $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir'        => $this->request->getPost('tanggal_lahir'),
            'berat_badan_lahir'    => $this->request->getPost('berat_badan_lahir') ?: null,
            'panjang_badan_lahir'  => $this->request->getPost('panjang_badan_lahir') ?: null,
            'lingkar_kepala_lahir' => $this->request->getPost('lingkar_kepala_lahir') ?: null,
            'premature'            => $this->request->getPost('premature') ?: 'Mature',
            'no_kk'                => trim((string) $this->request->getPost('no_kk')) ?: null,
            'nik_ibu'              => trim((string) $this->request->getPost('nik_ibu')) ?: null,
            'nama_ibu'             => trim((string) $this->request->getPost('nama_ibu')) ?: null,
            'nik_ayah'             => trim((string) $this->request->getPost('nik_ayah')) ?: null,
            'nama_ayah'            => trim((string) $this->request->getPost('nama_ayah')) ?: null,
        ];

        $this->anakModel->update($id, $data);

        // Jika anak belum punya akun orang tua, buat/hubungkan sekarang
        $anakBaru = $this->anakModel->find($id);
        $pesan    = 'Data anak berhasil diperbarui!';
        if ($anakBaru && empty($anakBaru['user_id'])) {
            $akun = (new AkunOrangTua())->pastikanAkun((int) $id);
            if ($akun['baru']) {
                $pesan .= ' Akun orang tua dibuat otomatis (username & password awal = NIK).';
            }
        }

        return redirect()->to('/anak')->with('success', $pesan);
    }

    // Hapus permanen dinonaktifkan. Gunakan fitur Arsip (status keaktifan)
    // untuk mengeluarkan anak dari daftar aktif tanpa menghilangkan riwayat datanya.
    public function delete($id)
    {
        return redirect()->to('/anak')->with('error', 'Hapus data anak telah dinonaktifkan. Gunakan fitur Arsip (ubah Status menjadi Lulus/Pindah/Lainnya) untuk mengeluarkan anak dari daftar aktif.');
    }

    // Detail anak & riwayat pengukuran
    public function detail($id)
    {
        $anak = $this->anakModel->find($id);
        $pengukuran = $this->pengukuranModel
            ->where('anak_id', $id)
            ->orderBy('tanggal_ukur', 'DESC')
            ->findAll();

        $data = [
            'title'      => 'Detail Anak - ' . $anak['nama'],
            'anak'       => $anak,
            'pengukuran' => $pengukuran
        ];
        return view('anak/detail', $data);
    }

    // Form input pengukuran baru
    public function inputPengukuran()
    {
        $data = [
            'title'          => 'Input Pengukuran',
            'anak'           => $this->anakModel->where('status_keaktifan', 'Aktif')->findAll(),
            'tanggalHariIni' => $this->tanggalHariIni(),
        ];
        return view('anak/input_pengukuran', $data);
    }

    /**
     * Tanggal hari ini (zona waktu WIB). Tanggal pengukuran selalu memakai ini
     * dan tidak bisa dipilih manual. Zona waktu dipaksa Asia/Jakarta karena
     * appTimezone aplikasi masih UTC (tanggal akan mundur sehari pukul 00.00-06.59 WIB).
     */
    private function tanggalHariIni(): string
    {
        return (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');
    }

    // Simpan pengukuran
    public function simpanPengukuran($anak_id = null)
    {
        helper('zscore');

        $rules = [
            'anak_id'        => 'required|integer',
            'berat_badan'    => 'required|decimal|greater_than[0]|less_than[100]',
            'tinggi_badan'   => 'required|decimal|greater_than[20]|less_than[150]',
            'lingkar_kepala' => 'permit_empty|decimal|greater_than[20]|less_than[70]',
            'lila'           => 'permit_empty|decimal|greater_than[5]|less_than[40]',
            'keterangan'     => 'permit_empty|max_length[1000]',
        ];

        $messages = [
            'anak_id' => [
                'required' => 'Anak wajib dipilih.',
                'integer'  => 'Data anak tidak valid.',
            ],
            'berat_badan' => [
                'required'     => 'Berat badan wajib diisi.',
                'decimal'      => 'Berat badan harus berupa angka.',
                'greater_than' => 'Berat badan harus lebih dari 0 kg.',
                'less_than'    => 'Berat badan tidak masuk akal.',
            ],
            'tinggi_badan' => [
                'required'     => 'Panjang/Tinggi badan wajib diisi.',
                'decimal'      => 'Panjang/Tinggi badan harus berupa angka.',
                'greater_than' => 'Panjang/Tinggi badan harus lebih dari 20 cm.',
                'less_than'    => 'Panjang/Tinggi badan tidak masuk akal.',
            ],
            'lingkar_kepala' => [
                'decimal'      => 'Lingkar kepala harus berupa angka.',
                'greater_than' => 'Lingkar kepala harus lebih dari 20 cm.',
                'less_than'    => 'Lingkar kepala tidak masuk akal.',
            ],
            'lila' => [
                'decimal'      => 'LILA harus berupa angka.',
                'greater_than' => 'LILA harus lebih dari 5 cm.',
                'less_than'    => 'LILA tidak masuk akal.',
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 1000 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/anak/input-pengukuran')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $anak_id     = $this->request->getPost('anak_id');
        $beratBadan  = $this->request->getPost('berat_badan');
        $tinggiBadan = $this->request->getPost('tinggi_badan');
        // Tanggal ukur dipaksa hari ini dari server (input tanggal dari form diabaikan)
        $tanggalUkur = $this->tanggalHariIni();

        // Ambil data anak
        $anak = $this->anakModel->find($anak_id);

        if (!$anak) {
            return redirect()->to('/anak/input-pengukuran')
                ->withInput()
                ->with('error', 'Data anak tidak ditemukan.');
        }

        if (($anak['status_keaktifan'] ?? '') !== 'Aktif') {
            return redirect()->to('/anak/input-pengukuran')
                ->withInput()
                ->with('error', 'Data anak tidak aktif, tidak bisa ditambahkan pengukuran.');
        }

        // Cegah pengukuran ganda: 1 anak hanya boleh 1 data per tanggal ukur
        $sudahAda = $this->pengukuranModel
            ->where('anak_id', $anak_id)
            ->where('tanggal_ukur', $tanggalUkur)
            ->first();

        if ($sudahAda) {
            return redirect()->to('/anak/input-pengukuran')
                ->withInput()
                ->with('error', 'Pengukuran untuk ' . $anak['nama'] . ' pada tanggal '
                    . date('d-m-Y', strtotime($tanggalUkur))
                    . ' sudah ada. Satu anak hanya bisa diinput satu kali per tanggal.');
        }

        // Hitung Z-Score
        $db     = \Config\Database::connect();
        $zscore = hitungZScore(
            $beratBadan,
            $tinggiBadan,
            $anak['tanggal_lahir'],
            $tanggalUkur,
            $anak['jenis_kelamin'],
            $db
        );

        $this->pengukuranModel->save([
            'anak_id'        => $anak_id,
            'tanggal_ukur'   => $tanggalUkur,
            'berat_badan'    => $beratBadan,
            'tinggi_badan'   => $tinggiBadan,
            'lingkar_kepala' => $this->request->getPost('lingkar_kepala') ?: null,
            'lila'           => $this->request->getPost('lila') ?: null,
            'keterangan'     => trim((string) $this->request->getPost('keterangan')) ?: null,
            'z_score_bb_u'   => $zscore['z_score_bb_u'],
            'z_score_tb_u'   => $zscore['z_score_tb_u'],
            'z_score_bb_tb'  => $zscore['z_score_bb_tb'],
            'status_gizi'    => $zscore['status_gizi'],
        ]);

        // Status N/T/O/B ditentukan otomatis dari riwayat penimbangan anak
        $this->pengukuranModel->sinkronkanNTOB((int) $anak_id, $tanggalUkur);
        $tersimpan = $this->pengukuranModel
            ->where('anak_id', $anak_id)
            ->where('tanggal_ukur', $tanggalUkur)
            ->first();
        $ntobHasil = $tersimpan['ntob'] ?? '-';

        // Kirim alert otomatis ke orang tua bila hasil masuk zona berisiko
        try {
            (new \App\Libraries\NotifikasiService())->alertPertumbuhan($anak, $zscore, $tanggalUkur);
        } catch (\Throwable $e) {
            log_message('error', 'Alert pertumbuhan gagal: ' . $e->getMessage());
        }

        return redirect()->to('/anak/detail/' . $anak_id)
            ->with('success', 'Data pengukuran berhasil disimpan! Status Gizi BB/U: ' . $zscore['status_gizi'] . ' | Status PB/U atau TB/U: ' . ($zscore['status_tb'] ?? '-') . ' | Status Gizi BB/TB: ' . ($zscore['status_gizi_bbtb'] ?? '-') . ' | N/T/O/B: ' . $ntobHasil);
    }
    public function hasilPengukuran()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $pengukuran = $this->pengukuranModel
            ->select('pengukuran.*, anak.nama, anak.nik_anak, anak.jenis_kelamin, anak.tanggal_lahir')
            ->join('anak', 'anak.id = pengukuran.anak_id')
            ->where('MONTH(pengukuran.tanggal_ukur)', $bulan)
            ->where('YEAR(pengukuran.tanggal_ukur)', $tahun)
            ->orderBy('pengukuran.tanggal_ukur', 'ASC')
            ->findAll();

        $data = [
            'title'      => 'Hasil Pengukuran',
            'pengukuran' => $pengukuran,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
        ];
        return view('anak/hasil_pengukuran', $data);
    }

    // Import Excel
    public function importExcel()
    {
        return view('anak/import');
    }

    public function importProcess()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->to('/anak/import')->with('error', 'File tidak valid!');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray();

        $imported = 0;
        $akunBaru = 0;
        foreach ($rows as $index => $row) {
            if ($index === 0) continue;
            if (empty($row[1])) continue;

            // Cek apakah NIK sudah ada
            $existing = $this->anakModel->where('nik_anak', $row[1])->first();
            if ($existing) continue;

            // Konversi jenis kelamin
            $jenis_kelamin = $row[3] ?? '';
            if ($jenis_kelamin === 'P') {
                $jenis_kelamin = 'Perempuan';
            } elseif ($jenis_kelamin === 'L') {
                $jenis_kelamin = 'Laki-laki';
            }

            // Konversi tanggal lahir
            $tanggal_lahir = $row[4] ?? '';
            if (is_object($tanggal_lahir)) {
                $tanggal_lahir = $tanggal_lahir->format('Y-m-d');
            } else {
                $tanggal_lahir = date('Y-m-d', strtotime($tanggal_lahir));
            }

            $this->anakModel->save([
                'nik_anak'            => $row[1] ?? '',
                'nama'                => $row[2] ?? '',
                'jenis_kelamin'       => $jenis_kelamin,
                'tanggal_lahir'       => $tanggal_lahir,
                'berat_badan_lahir'   => is_numeric($row[5] ?? null) ? round($row[5] / 1000, 2) : 0,
                'panjang_badan_lahir' => $row[6] ?? 0,
                'lingkar_kepala_lahir'=> $row[7] ?? 0,
                'premature'           => $row[8] ?? 'Mature',
                'no_kk'               => $row[9] ?? '',
                'nik_ibu'             => trim((string) ($row[10] ?? '')),
                'nama_ibu'            => trim((string) ($row[11] ?? '')),
                'nik_ayah'            => trim((string) ($row[12] ?? '')),
                'nama_ayah'           => trim((string) ($row[13] ?? '')),
            ]);
            $akun = (new AkunOrangTua())->pastikanAkun((int) $this->anakModel->getInsertID());
            if ($akun['baru']) $akunBaru++;
            $imported++;
        }

        return redirect()->to('/anak')->with('success', $imported . ' data anak berhasil diimport! ' . $akunBaru . ' akun orang tua baru dibuat.');
    }
    // Ubah status keaktifan anak (Aktif/Lulus/Pindah/Lainnya)
    public function ubahStatus($id = null)
    {
        $anak = $this->anakModel->find($id);
        if (!$anak) {
            return redirect()->to('/anak')->with('error', 'Data anak tidak ditemukan!');
        }

        $status  = $this->request->getPost('status_keaktifan');
        $allowed = ['Aktif', 'Lulus', 'Pindah', 'Lainnya'];
        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid!');
        }

        $tanggal = $this->request->getPost('tanggal_status');
        $alasan  = $this->request->getPost('alasan_status');

        $this->anakModel->update($id, [
            'status_keaktifan' => $status,
            'tanggal_status'   => ($status === 'Aktif') ? null : ($tanggal ?: date('Y-m-d')),
            'alasan_status'    => ($status === 'Aktif') ? null : $alasan,
        ]);

        return redirect()->back()->with('success', 'Status keaktifan ' . $anak['nama'] . ' diperbarui menjadi ' . $status . '.');
    }

    // Form import pengukuran massal (Excel)
    public function importPengukuran()
    {
        return view('anak/import_pengukuran');
    }

    // Proses import data pengukuran dari Excel
    public function importPengukuranProcess()
    {
        helper('zscore');

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->to('/anak/import-pengukuran')->with('error', 'File tidak valid!');
        }

        if ($file->getSizeByUnit('mb') > 2) {
            return redirect()->to('/anak/import-pengukuran')
                ->with('error', 'Ukuran file maksimal 2MB!');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->to('/anak/import-pengukuran')->with('error', 'Format file harus .xlsx atau .xls!');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        } catch (\Exception $e) {
            return redirect()->to('/anak/import-pengukuran')->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray();

        $db       = \Config\Database::connect();
        $imported = 0;
        $dilewati = 0;
        $gagal    = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;
            if (empty($row[1])) continue;

            $nik  = trim((string) $row[1]);
            $anak = $this->anakModel->where('nik_anak', $nik)->first();
            if (!$anak) {
                $gagal[] = $nik;
                continue;
            }

            $tanggalUkur = $row[2] ?? '';

            if ($tanggalUkur === null || $tanggalUkur === '') {
                $gagal[] = $nik . ' (tanggal ukur kosong)';
                continue;
            }

            if (is_object($tanggalUkur)) {
                $tanggalUkur = $tanggalUkur->format('Y-m-d');
            } elseif (is_numeric($tanggalUkur)) {
                $tanggalUkur = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalUkur)->format('Y-m-d');
            } else {
                $timestamp = strtotime((string) $tanggalUkur);

                if ($timestamp === false) {
                    $gagal[] = $nik . ' (tanggal ukur tidak valid)';
                    continue;
                }

                $tanggalUkur = date('Y-m-d', $timestamp);
            }

            $beratBadan  = $row[3] ?? null;
            $tinggiBadan = $row[4] ?? null;

            $beratRaw  = str_replace(',', '.', trim((string) $beratBadan));
            $tinggiRaw = str_replace(',', '.', trim((string) $tinggiBadan));

            if ($beratRaw === '' || $tinggiRaw === '') {
                $gagal[] = $nik . ' (BB/TB kosong)';
                continue;
            }

            if (!is_numeric($beratRaw) || !is_numeric($tinggiRaw)) {
                $gagal[] = $nik . ' (BB/TB harus angka)';
                continue;
            }

            $beratBadan  = (float) $beratRaw;
            $tinggiBadan = (float) $tinggiRaw;

            if ($beratBadan <= 0 || $beratBadan >= 100 || $tinggiBadan <= 20 || $tinggiBadan >= 150) {
                $gagal[] = $nik . ' (BB/TB tidak masuk akal)';
                continue;
            }

            $existing = $this->pengukuranModel
                ->where('anak_id', $anak['id'])
                ->where('tanggal_ukur', $tanggalUkur)
                ->first();
            if ($existing) {
                $dilewati++;
                continue;
            }

            $zscore = hitungZScore(
                $beratBadan,
                $tinggiBadan,
                $anak['tanggal_lahir'],
                $tanggalUkur,
                $anak['jenis_kelamin'],
                $db
            );

            $lk   = trim((string) ($row[5] ?? ''));
            $lila = trim((string) ($row[6] ?? ''));
            // Kolom H (N/T/O/B) diabaikan: status dihitung otomatis
            $ket  = trim((string) ($row[8] ?? ''));

            $this->pengukuranModel->save([
                'anak_id'        => $anak['id'],
                'tanggal_ukur'   => $tanggalUkur,
                'berat_badan'    => $beratBadan,
                'tinggi_badan'   => $tinggiBadan,
                'lingkar_kepala' => ($lk === '' ? null : $lk),
                'lila'           => ($lila === '' ? null : $lila),
                'keterangan'     => ($ket === '' ? null : $ket),
                'z_score_bb_u'   => $zscore['z_score_bb_u'],
                'z_score_tb_u'   => $zscore['z_score_tb_u'],
                'z_score_bb_tb'  => $zscore['z_score_bb_tb'],
                'status_gizi'    => $zscore['status_gizi'],
            ]);

            // Hitung N/T/O/B otomatis (juga memperbaiki baris berikutnya bila file tidak urut)
            $this->pengukuranModel->sinkronkanNTOB((int) $anak['id'], $tanggalUkur);

            $imported++;
        }

        $pesan = $imported . ' data pengukuran berhasil diimport.';
        if ($dilewati > 0) {
            $pesan .= ' ' . $dilewati . ' data dilewati (tanggal ukur sudah ada).';
        }
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' baris gagal (NIK tidak ditemukan / data tidak lengkap): ' . implode(', ', array_slice($gagal, 0, 10));
            if (count($gagal) > 10) {
                $pesan .= ', dst.';
            }
        }

        return redirect()->to('/anak/hasil-pengukuran')->with('success', $pesan);
    }

    // Download template Excel untuk import pengukuran
    public function templatePengukuran()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pengukuran');

        $headers = ['No', 'NIK Anak', 'Tanggal Ukur (YYYY-MM-DD)', 'Berat Badan (kg)', 'Tinggi Badan (cm)', 'Lingkar Kepala (cm)', 'LiLA (cm)', 'N/T/O/B (otomatis, kosongkan)', 'Keterangan'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . '1', $header);
        }

        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', '3402172208240003');
        $sheet->setCellValue('C2', date('Y-m-d'));
        $sheet->setCellValue('D2', 9.65);
        $sheet->setCellValue('E2', 80.30);
        $sheet->setCellValue('F2', 46.5);
        $sheet->setCellValue('G2', 14.5);
        $sheet->setCellValue('I2', 'Contoh - hapus baris ini sebelum import');

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template-import-pengukuran.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    // Export PDF hasil pengukuran
    public function exportPdfPengukuran()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $pengukuran = $this->pengukuranModel
            ->select('pengukuran.*, anak.nama, anak.nik_anak, anak.jenis_kelamin, anak.tanggal_lahir')
            ->join('anak', 'anak.id = pengukuran.anak_id')
            ->where('MONTH(pengukuran.tanggal_ukur)', $bulan)
            ->where('YEAR(pengukuran.tanggal_ukur)', $tahun)
            ->orderBy('pengukuran.tanggal_ukur', 'ASC')
            ->findAll();

        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $data = [
            'title'      => 'Hasil Pengukuran',
            'pengukuran' => $pengukuran,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'namaBulan'  => $namaBulan[$bulanKey] . ' ' . $tahun,
        ];

        $dompdf = new \Dompdf\Dompdf();
        $html   = view('anak/pdf_pengukuran', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('hasil-pengukuran-' . $namaBulan[$bulanKey] . '-' . $tahun . '.pdf', ['Attachment' => true]);
    }

    // Export Excel hasil pengukuran
    public function exportExcelPengukuran()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $pengukuran = $this->pengukuranModel
            ->select('pengukuran.*, anak.nama, anak.nik_anak, anak.jenis_kelamin, anak.tanggal_lahir')
            ->join('anak', 'anak.id = pengukuran.anak_id')
            ->where('MONTH(pengukuran.tanggal_ukur)', $bulan)
            ->where('YEAR(pengukuran.tanggal_ukur)', $tahun)
            ->orderBy('pengukuran.tanggal_ukur', 'ASC')
            ->findAll();

        $namaBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hasil Pengukuran');

        // Judul
        $sheet->setCellValue('A1', 'HASIL PENGUKURAN POSYANDU NUSA INDAH');
        $sheet->setCellValue('A2', 'Bulan: ' . $namaBulan[$bulanKey] . ' ' . $tahun);
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');

        // Header
        $headers = ['No', 'Tanggal Ukur', 'NIK Anak', 'Nama Anak', 'JK', 'Umur (bln)',
                    'BB (kg)', 'PB atau TB (cm)', 'LK (cm)', 'LiLA (cm)', 'N/T/O/B',
                    'Z-Score BB/U', 'Z-Score PB/U atau TB/U', 'Status Gizi'];

        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '4', $header);
        }

        // Data
        $row = 5;
        $no  = 1;
        foreach ($pengukuran as $p) {
            $tglLahir  = new \DateTime($p['tanggal_lahir']);
            $tglUkur   = new \DateTime($p['tanggal_ukur']);
            $umur      = $tglLahir->diff($tglUkur);
            $umurBulan = ($umur->y * 12) + $umur->m;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($p['tanggal_ukur'])));
            $sheet->setCellValue('C' . $row, $p['nik_anak']);
            $sheet->setCellValue('D' . $row, $p['nama']);
            $sheet->setCellValue('E' . $row, $p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L');
            $sheet->setCellValue('F' . $row, $umurBulan);
            $sheet->setCellValue('G' . $row, $p['berat_badan']);
            $sheet->setCellValue('H' . $row, $p['tinggi_badan']);
            $sheet->setCellValue('I' . $row, $p['lingkar_kepala'] ?? '-');
            $sheet->setCellValue('J' . $row, $p['lila'] ?? '-');
            $sheet->setCellValue('K' . $row, $p['ntob'] ?? '-');
            $sheet->setCellValue('L' . $row, $p['z_score_bb_u'] ?? '-');
            $sheet->setCellValue('M' . $row, $p['z_score_tb_u'] ?? '-');
            $sheet->setCellValue('N' . $row, $p['status_gizi'] ?? '-');
            $row++;
        }

        // Auto size
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="hasil-pengukuran-' . $namaBulan[$bulanKey] . '-' . $tahun . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getPeriodeFromRequest(): array
    {
        $bulan = (int) ($this->request->getGet('bulan') ?? date('m'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('m');
        }

        $tahunSekarang = (int) date('Y');

        if ($tahun < 2020 || $tahun > ($tahunSekarang + 1)) {
            $tahun = $tahunSekarang;
        }

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
        ];
    }
}