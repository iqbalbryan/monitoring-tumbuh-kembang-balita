<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/artikel/(:num)', 'Home::artikel/$1');

// Routes Auth
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginProcess');
$routes->post('/logout', 'Auth::logout');

// Routes Admin (harus login & role admin)
$routes->group('', ['filter' => 'admin'], function($routes) {
    // Dashboard & User Management
    $routes->get('/admin/dashboard', 'Admin::dashboard');
    $routes->get('/admin/users', 'Admin::users');
    $routes->get('/admin/users/detail/(:num)', 'Admin::userDetail/$1');
    $routes->post('/admin/users/toggle/(:num)', 'Admin::toggleStatus/$1');
    $routes->post('/admin/kirim-notifikasi', 'Admin::jalankanNotifikasi');

    // Data Anak
    $routes->get('/anak', 'Anak::index');
    $routes->get('/anak/create', 'Anak::create');
    $routes->post('/anak/store', 'Anak::store');
    $routes->get('/anak/edit/(:num)', 'Anak::edit/$1');
    $routes->post('/anak/update/(:num)', 'Anak::update/$1');
    $routes->get('/anak/detail/(:num)', 'Anak::detail/$1');
    $routes->get('/anak/import', 'Anak::importExcel');
    $routes->post('/anak/import', 'Anak::importProcess');
    $routes->get('/anak/input-pengukuran', 'Anak::inputPengukuran');
    $routes->post('/anak/simpan-pengukuran', 'Anak::simpanPengukuran');
    $routes->get('/anak/hasil-pengukuran', 'Anak::hasilPengukuran');
    $routes->get('/anak/import-pengukuran', 'Anak::importPengukuran');
    $routes->post('/anak/import-pengukuran', 'Anak::importPengukuranProcess');
    $routes->get('/anak/template-pengukuran', 'Anak::templatePengukuran');
    $routes->post('/anak/ubah-status/(:num)', 'Anak::ubahStatus/$1');

    //jadwal
    $routes->get('/admin/jadwal', 'Jadwal::index');
    $routes->get('/admin/jadwal/create', 'Jadwal::create');
    $routes->post('/admin/jadwal/store', 'Jadwal::store');
    $routes->get('/admin/jadwal/edit/(:num)', 'Jadwal::edit/$1');
    $routes->post('/admin/jadwal/update/(:num)', 'Jadwal::update/$1');

    $routes->get('/admin/artikel', 'Artikel::index');
    $routes->get('/admin/artikel/create', 'Artikel::create');
    $routes->post('/admin/artikel/store', 'Artikel::store');
    $routes->get('/admin/artikel/edit/(:num)', 'Artikel::edit/$1');
    $routes->post('/admin/artikel/update/(:num)', 'Artikel::update/$1');
    $routes->post('/admin/artikel/delete/(:num)', 'Artikel::delete/$1');

    $routes->get('/admin/laporan', 'Laporan::index');
    $routes->get('/admin/laporan/export-pdf', 'Laporan::exportPdf');
    $routes->get('/admin/laporan/export-excel', 'Laporan::exportExcel');

    $routes->get('/anak/export-pdf-pengukuran', 'Anak::exportPdfPengukuran');
    $routes->get('/anak/export-excel-pengukuran', 'Anak::exportExcelPengukuran');
});

// Routes Orang Tua (harus login)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/orangtua/dashboard', 'OrangTua::dashboard');
    $routes->get('/orangtua/anak/(:num)', 'OrangTua::detailAnak/$1');
    $routes->get('/orangtua/tambah-anak', 'OrangTua::tambahAnak');
    $routes->post('/orangtua/simpan-anak', 'OrangTua::simpanAnak');
    $routes->get('/orangtua/ganti-password', 'Password::form');
    $routes->post('/orangtua/ganti-password', 'Password::simpan');
    $routes->get('/orangtua/notifikasi', 'OrangTua::notifikasi');
    $routes->post('/orangtua/baca-notifikasi', 'OrangTua::bacaNotifikasi');
    $routes->get('/orangtua/artikel/(:num)', 'OrangTua::artikelDetail/$1');
});