<?php

namespace App\Libraries;

use App\Models\AnakModel;
use App\Models\UserModel;

/**
 * Membuat akun orang tua otomatis dari data anak.
 * Dipanggil dari Anak::store, Anak::update, dan Anak::importProcess.
 *
 * Aturan:
 * - username & password awal = NIK ibu (jika kosong, NIK ayah)
 * - NIK yang sudah punya akun tidak dibuat ulang
 * - semua anak dengan NIK ibu/ayah yang sama dihubungkan ke akun itu
 */
class AkunOrangTua
{
    /**
     * @return array{user_id:?int, baru:bool}
     */
    public function pastikanAkun(int $anakId): array
    {
        $anakModel = new AnakModel();
        $userModel = new UserModel();

        $anak = $anakModel->find($anakId);
        if (! $anak) {
            return ['user_id' => null, 'baru' => false];
        }

        $nikIbu  = trim((string) ($anak['nik_ibu'] ?? ''));
        $nikAyah = trim((string) ($anak['nik_ayah'] ?? ''));

        // Pilih NIK dan nama: ibu dulu, kalau kosong pakai ayah
        if ($nikIbu !== '') {
            $nik  = $nikIbu;
            $nama = trim((string) ($anak['nama_ibu'] ?? ''));
        } elseif ($nikAyah !== '') {
            $nik  = $nikAyah;
            $nama = trim((string) ($anak['nama_ayah'] ?? ''));
        } else {
            return ['user_id' => null, 'baru' => false];
        }

        if ($nama === '') {
            $nama = 'Orang Tua dari ' . ($anak['nama'] ?: 'Anak');
        }

        $baru = false;
        $user = $userModel->where('nik', $nik)->first();

        if ($user) {
            $userId = (int) $user['id'];
        } else {
            $userModel->insert([
                'nama'     => mb_substr($nama, 0, 100),
                'nik'      => $nik,
                'email'    => null,
                'password' => password_hash($nik, PASSWORD_DEFAULT),
                'role'     => 'orangtua',
                'status'   => 'aktif',
            ]);
            $userId = (int) $userModel->getInsertID();
            $baru   = true;
        }

        // Hubungkan semua anak (kakak/adik) dengan NIK ini yang belum punya akun
        db_connect()->table('anak')
            ->groupStart()
                ->where('nik_ibu', $nik)
                ->orWhere('nik_ayah', $nik)
            ->groupEnd()
            ->where('user_id IS NULL', null, false)
            ->update(['user_id' => $userId]);

        return ['user_id' => $userId, 'baru' => $baru];
    }
}
