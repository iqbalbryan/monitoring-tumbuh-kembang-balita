<?php

namespace App\Controllers;

use App\Models\ArtikelModel;

class Artikel extends BaseController
{
    protected $artikelModel;

    public function __construct()
    {
        $this->artikelModel = new ArtikelModel();
    }

    // Lihat semua artikel
    public function index()
    {
        $data = [
            'title'   => 'Manajemen Artikel',
            'artikel' => $this->artikelModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('artikel/index', $data);
    }

    // Form tambah artikel
    public function create()
    {
        $data = ['title' => 'Tambah Artikel'];
        return view('artikel/create', $data);
    }

    // Simpan artikel
    public function store()
    {
        $rules = [
            'judul'    => 'required|min_length[3]|max_length[150]',
            'konten'   => 'required|min_length[10]',
            'kategori' => 'required|in_list[Gizi,Tumbuh Kembang,Imunisasi,Kesehatan,Lainnya]',
            'status'   => 'required|in_list[draft,publikasi]',
        ];

        $messages = [
            'judul' => [
                'required'   => 'Judul artikel wajib diisi.',
                'min_length' => 'Judul artikel minimal 3 karakter.',
                'max_length' => 'Judul artikel maksimal 150 karakter.',
            ],
            'konten' => [
                'required'   => 'Konten artikel wajib diisi.',
                'min_length' => 'Konten artikel minimal 10 karakter.',
            ],
            'kategori' => [
                'required'   => 'Kategori artikel wajib diisi.',
                'max_length' => 'Kategori artikel maksimal 100 karakter.',
            ],
            'status' => [
                'required' => 'Status artikel wajib dipilih.',
                'in_list'  => 'Status artikel tidak valid.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/artikel/create')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->artikelModel->save([
            'judul'    => trim($this->request->getPost('judul')),
            'konten'   => trim($this->request->getPost('konten')),
            'kategori' => trim($this->request->getPost('kategori')),
            'status'   => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/artikel')->with('success', 'Artikel berhasil ditambahkan!');
    }

    // Form edit artikel
    public function edit($id)
    {
        $data = [
            'title'   => 'Edit Artikel',
            'artikel' => $this->artikelModel->find($id)
        ];
        return view('artikel/edit', $data);
    }

    // Update artikel
    public function update($id)
    {
        $artikel = $this->artikelModel->find($id);

        if (!$artikel) {
            return redirect()->to('/admin/artikel')
                ->with('error', 'Data artikel tidak ditemukan.');
        }

        $rules = [
            'judul'    => 'required|min_length[3]|max_length[150]',
            'konten'   => 'required|min_length[10]',
            'kategori' => 'required|in_list[Gizi,Tumbuh Kembang,Imunisasi,Kesehatan,Lainnya]',
            'status'   => 'required|in_list[draft,publikasi]',
        ];

        $messages = [
            'judul' => [
                'required'   => 'Judul artikel wajib diisi.',
                'min_length' => 'Judul artikel minimal 3 karakter.',
                'max_length' => 'Judul artikel maksimal 150 karakter.',
            ],
            'konten' => [
                'required'   => 'Konten artikel wajib diisi.',
                'min_length' => 'Konten artikel minimal 10 karakter.',
            ],
            'kategori' => [
                'required' => 'Kategori artikel wajib dipilih.',
                'in_list'  => 'Kategori artikel tidak valid.',
            ],
            'status' => [
                'required' => 'Status artikel wajib dipilih.',
                'in_list'  => 'Status artikel tidak valid.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/admin/artikel/edit/' . $id)
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->artikelModel->update($id, [
            'judul'    => trim($this->request->getPost('judul')),
            'konten'   => trim($this->request->getPost('konten')),
            'kategori' => trim($this->request->getPost('kategori')),
            'status'   => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/artikel')->with('success', 'Artikel berhasil diupdate!');
    }

    // Hapus artikel
    public function delete($id)
    {
        $this->artikelModel->delete($id);
        return redirect()->to('/admin/artikel')->with('success', 'Artikel berhasil dihapus!');
    }
}