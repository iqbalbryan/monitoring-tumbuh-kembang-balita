<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    // Show login form
    public function login()
    {
        if (session()->get('logged_in')) {
            if (session()->get('user_role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/orangtua/dashboard');
        }
        return view('auth/login');
    }

    // Process login (identitas = email atau NIK)
    public function loginProcess()
    {
        $identitas = trim((string) $this->request->getPost('identitas'));
        $password  = (string) $this->request->getPost('password');

        $user = null;
        if ($identitas !== '') {
            $user = $this->model
                ->groupStart()
                    ->where('email', $identitas)
                    ->orWhere('nik', $identitas)
                ->groupEnd()
                ->first();
        }

        if ($user && password_verify($password, $user['password'])) {

            // Cek status akun (hanya akun nonaktif yang diblokir)
            if ($user['status'] === 'nonaktif') {
                return redirect()->to('/login')->with('error', 'Akun kamu dinonaktifkan oleh admin. Hubungi posyandu untuk informasi lebih lanjut.');
            }

            // Set session
            session()->set([
                'logged_in'  => true,
                'user_id'    => $user['id'],
                'user_nama'  => $user['nama'],
                'user_email' => $user['email'],
                'user_role'  => $user['role'],
                'user_nik'   => $user['nik'],
            ]);

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            return redirect()->to('/orangtua/dashboard');
        }

        return redirect()->to('/login')->with('error', 'Email/NIK atau password salah!');
    }

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
