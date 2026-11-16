<?php

namespace App\Controllers;

use App\Models\JadwalModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
    }

    // Lihat semua jadwal: mendatang (terdekat dulu) dan riwayat (terbaru dulu)
    public function index()
    {
        $semua = $this->jadwalModel
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        $mendatang = [];
        $riwayat   = [];
        foreach ($semua as $j) {
            if (JadwalModel::sudahSelesai($j)) {
                $riwayat[] = $j;
            } else {
                $mendatang[] = $j;
            }
        }

        $data = [
            'title'     => 'Jadwal Posyandu',
            'mendatang' => $mendatang,
            'riwayat'   => array_reverse($riwayat),
        ];
        return view('jadwal/index', $data);
    }

    // Form tambah jadwal
    public function create()
    {
        $data = ['title' => 'Tambah Jadwal'];
        return view('jadwal/create', $data);
    }

    // Simpan jadwal
    public function store()
    {
        $rules = [
            'judul'         => 'required|min_length[3]|max_length[150]',
            'tanggal'       => 'required|valid_date[Y-m-d]',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required',
            'lokasi'        => 'required|max_length[255]',
            'keterangan'    => 'permit_empty|max_length[1000]',
        ];

        $messages = [
            'judul' => [
                'required'   => 'Judul jadwal wajib diisi.',
                'min_length' => 'Judul jadwal minimal 3 karakter.',
                'max_length' => 'Judul jadwal maksimal 150 karakter.',
            ],
            'tanggal' => [
                'required'   => 'Tanggal jadwal wajib diisi.',
                'valid_date' => 'Format tanggal jadwal tidak valid.',
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai wajib diisi.',
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai wajib diisi.',
            ],
            'lokasi' => [
                'required'   => 'Lokasi wajib diisi.',
                'max_length' => 'Lokasi maksimal 255 karakter.',
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 1000 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/jadwal/create')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');

        if ($waktuSelesai <= $waktuMulai) {
            return redirect()->to('/admin/jadwal/create')
                ->withInput()
                ->with('error', 'Waktu selesai harus setelah waktu mulai.');
        }

        $this->jadwalModel->save([
            'judul'         => trim($this->request->getPost('judul')),
            'tanggal'       => $this->request->getPost('tanggal'),
            'waktu_mulai'   => $this->request->getPost('waktu_mulai'),
            'waktu_selesai' => $this->request->getPost('waktu_selesai'),
            'lokasi'        => trim($this->request->getPost('lokasi')),
            'keterangan'    => trim((string) $this->request->getPost('keterangan')) ?: null,
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    // Form edit jadwal
    public function edit($id)
    {
        $jadwal = $this->jadwalModel->find($id);

        if (!$jadwal) {
            return redirect()->to('/admin/jadwal')
                ->with('error', 'Data jadwal tidak ditemukan.');
        }

        // Jadwal yang sudah selesai = riwayat pelaksanaan, tidak boleh diubah
        if (JadwalModel::sudahSelesai($jadwal)) {
            return redirect()->to('/admin/jadwal')
                ->with('error', 'Jadwal yang sudah selesai tidak dapat diedit.');
        }

        $data = [
            'title'  => 'Edit Jadwal',
            'jadwal' => $jadwal
        ];
        return view('jadwal/edit', $data);
    }

    // Update jadwal
    public function update($id)
    {
        $jadwal = $this->jadwalModel->find($id);

        if (!$jadwal) {
            return redirect()->to('/admin/jadwal')
                ->with('error', 'Data jadwal tidak ditemukan.');
        }

        if (JadwalModel::sudahSelesai($jadwal)) {
            return redirect()->to('/admin/jadwal')
                ->with('error', 'Jadwal yang sudah selesai tidak dapat diedit.');
        }

        $rules = [
            'judul'         => 'required|min_length[3]|max_length[150]',
            'tanggal'       => 'required|valid_date[Y-m-d]',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required',
            'lokasi'        => 'required|max_length[255]',
            'keterangan'    => 'permit_empty|max_length[1000]',
        ];

        $messages = [
            'judul' => [
                'required'   => 'Judul jadwal wajib diisi.',
                'min_length' => 'Judul jadwal minimal 3 karakter.',
                'max_length' => 'Judul jadwal maksimal 150 karakter.',
            ],
            'tanggal' => [
                'required'   => 'Tanggal jadwal wajib diisi.',
                'valid_date' => 'Format tanggal jadwal tidak valid.',
            ],
            'waktu_mulai' => [
                'required' => 'Waktu mulai wajib diisi.',
            ],
            'waktu_selesai' => [
                'required' => 'Waktu selesai wajib diisi.',
            ],
            'lokasi' => [
                'required'   => 'Lokasi wajib diisi.',
                'max_length' => 'Lokasi maksimal 255 karakter.',
            ],
            'keterangan' => [
                'max_length' => 'Keterangan maksimal 1000 karakter.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/jadwal/edit/' . $id)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $waktuMulai   = $this->request->getPost('waktu_mulai');
        $waktuSelesai = $this->request->getPost('waktu_selesai');

        if ($waktuSelesai <= $waktuMulai) {
            return redirect()->to('/admin/jadwal/edit/' . $id)
                ->withInput()
                ->with('error', 'Waktu selesai harus setelah waktu mulai.');
        }

        $this->jadwalModel->update($id, [
            'judul'         => trim($this->request->getPost('judul')),
            'tanggal'       => $this->request->getPost('tanggal'),
            'waktu_mulai'   => $this->request->getPost('waktu_mulai'),
            'waktu_selesai' => $this->request->getPost('waktu_selesai'),
            'lokasi'        => trim($this->request->getPost('lokasi')),
            'keterangan'    => trim((string) $this->request->getPost('keterangan')) ?: null,
        ]);

        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil diupdate!');
    }
}