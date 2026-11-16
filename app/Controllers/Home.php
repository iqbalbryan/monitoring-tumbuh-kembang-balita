<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\JadwalModel;

class Home extends BaseController
{
    public function index()
    {
        $artikelModel = new ArtikelModel();
        $jadwalModel  = new JadwalModel();

        $data = [
            // 3 artikel terbaru yang sudah dipublikasikan
            'artikelTerbaru'  => $artikelModel
                ->where('status', 'publikasi')
                ->orderBy('created_at', 'DESC')
                ->findAll(3),
            // 3 jadwal posyandu yang akan datang (hari ini ke depan)
            'jadwalMendatang' => $jadwalModel
                ->where('tanggal >=', date('Y-m-d'))
                ->orderBy('tanggal', 'ASC')
                ->findAll(3),
        ];

        return view('home/index', $data);
    }

    // Detail artikel versi publik (bisa dibuka tanpa login)
    public function artikel($id)
    {
        $artikelModel = new ArtikelModel();
        $artikel = $artikelModel->find($id);

        // Hanya artikel yang berstatus publikasi yang boleh dibuka publik
        if (!$artikel || ($artikel['status'] ?? '') !== 'publikasi') {
            return redirect()->to('/');
        }

        $lainnya = $artikelModel
            ->where('status', 'publikasi')
            ->where('id !=', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        return view('home/artikel', [
            'artikel' => $artikel,
            'lainnya' => $lainnya,
        ]);
    }
}
