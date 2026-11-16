<?php

namespace App\Controllers;

use App\Models\UserModel;

class Password extends BaseController
{
    // Form ganti password (orang tua)
    public function form()
    {
        return view('orangtua/ganti_password', [
            'title'        => 'Ganti Password',
            'pageTitle'    => 'Ganti Password',
            'pageSubtitle' => 'Ubah password akun kamu',
            'activeMenu'   => 'password',
        ]);
    }

    public function simpan()
    {
        $rules = [
            'password_lama'       => 'required',
            'password_baru'       => 'required|min_length[8]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ];
        $messages = [
            'password_lama' => ['required' => 'Password lama wajib diisi.'],
            'password_baru' => [
                'required'   => 'Password baru wajib diisi.',
                'min_length' => 'Password baru minimal 8 karakter.',
            ],
            'konfirmasi_password' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches'  => 'Konfirmasi password tidak sama.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to('/orangtua/ganti-password')
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user      = $userModel->find(session()->get('user_id'));

        if (! $user || ! password_verify((string) $this->request->getPost('password_lama'), $user['password'])) {
            return redirect()->to('/orangtua/ganti-password')
                ->with('error', 'Password lama salah.');
        }

        $userModel->update($user['id'], [
            'password' => password_hash((string) $this->request->getPost('password_baru'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/orangtua/ganti-password')
            ->with('success', 'Password berhasil diubah.');
    }
}
