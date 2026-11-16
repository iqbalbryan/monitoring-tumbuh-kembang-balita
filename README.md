# Sistem Monitoring Tumbuh Kembang Anak — Posyandu Nusa Indah

Aplikasi web untuk memantau pertumbuhan balita di Posyandu. Petugas (admin) menginput hasil penimbangan dan pengukuran, lalu sistem **menghitung Z-Score berdasarkan standar WHO**, menentukan status gizi, dan menampilkannya dalam **KMS digital** yang bisa dilihat orang tua kapan saja.

> Dibangun dengan CodeIgniter 4 sebagai proyek tugas akhir. Seluruh data pada repository ini adalah **data contoh (fiktif)**.

## Fitur

**Orang tua**
- Login memakai NIK. Akun dibuat otomatis saat anak didaftarkan ke Posyandu (password awal = NIK, dapat diganti sendiri)
- Rapor anak: hasil pengukuran terakhir, grafik tumbuh kembang (berat & tinggi badan), KMS digital (BB/U, TB/U, BB/TB), dan riwayat kunjungan Posyandu
- Jadwal Posyandu, artikel edukasi, dan notifikasi

**Admin**
- Dashboard statistik: sebaran status gizi, anak yang perlu diperhatikan, dan kandidat lulus
- Kelola data anak (filter aktif, kandidat lulus, arsip) dan ubah status keaktifan
- Input pengukuran dengan perhitungan otomatis: Z-Score, status gizi (underweight, stunting, wasting), dan keterangan **N/T/O/B** (Naik, Tidak naik, tidak ditimbang bulan lalu, Baru). Orang tua mendapat notifikasi otomatis bila hasilnya berisiko
- Kelola jadwal Posyandu (jadwal yang sudah selesai menjadi riwayat dan tidak dapat diubah) dan kelola artikel
- Manajemen akun orang tua
- Laporan bulanan dengan ekspor **PDF** dan **Excel**

**Sistem**
- Notifikasi terjadwal lewat perintah `php spark notifikasi:kirim`: pengingat jadwal Posyandu H-2, anak yang belum ditimbang, dan anak yang akan lulus

## Teknologi

| Bagian | Yang dipakai |
|---|---|
| Backend | PHP 8.1+, CodeIgniter 4.6 |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Chart.js 4, jQuery, Select2 |
| Laporan | Dompdf (PDF), PhpSpreadsheet (Excel) |

## Cara Perhitungan Z-Score

Z-Score dihitung terhadap median dan simpangan baku pada tabel standar WHO sesuai jenis kelamin dan umur (atau tinggi badan untuk BB/TB). Penyebutnya bergantung pada posisi nilai terhadap median, karena sebaran data WHO tidak simetris:

```php
if ($nilaiAnak < $median) {
    $zscore = ($nilaiAnak - $median) / ($median - $sd1neg);   // di bawah median
} else {
    $zscore = ($nilaiAnak - $median) / ($sd1pos - $median);   // di atas / sama dengan median
}
```

Tiga indikator yang dihitung: **BB/U** (berat badan menurut umur), **TB/U** (tinggi badan menurut umur), dan **BB/TB** (berat badan menurut tinggi badan). Kodenya ada di `app/Helpers/zscore_helper.php`, dan tabel acuannya (`who_bb_u`, `who_tb_u`, `who_bb_tb`) disertakan di file SQL.

## Tangkapan Layar

| Dashboard Admin | Dashboard Orang Tua |
|---|---|
| ![Dashboard Admin](docs/screenshots/dashboard-admin.png) | ![Dashboard Orang Tua](docs/screenshots/dashboard-orangtua.png) |

| KMS Digital | Input Pengukuran |
|---|---|
| ![KMS Digital](docs/screenshots/kms-digital.png) | ![Input Pengukuran](docs/screenshots/input-pengukuran.png) |

## Instalasi

**Prasyarat:** PHP 8.1 atau lebih baru (ekstensi `intl` dan `mbstring` aktif), Composer, dan MySQL/MariaDB.

1. Clone repository dan masuk ke foldernya

   ```bash
   git clone https://github.com/iqbalbryan/monitoring-tumbuh-kembang-balita.git
   cd monitoring-tumbuh-kembang-balita
   ```

2. Pasang dependensi

   ```bash
   composer install
   ```

3. Salin file konfigurasi, lalu sesuaikan kredensial database di `.env`

   ```bash
   cp .env.example .env        # Windows: copy .env.example .env
   ```

4. Buat database bernama `nusaindah_db`, lalu impor `database/nusaindah_db.sql` (sudah termasuk tabel standar WHO dan data contoh)

5. Jalankan aplikasi

   ```bash
   php spark serve
   ```

   Buka http://localhost:8080

### Akun demo

| Peran | Login | Password |
|---|---|---|
| Admin | _(isi sesuai file SQL)_ | _(isi sesuai file SQL)_ |
| Orang tua | _(NIK contoh)_ | _(sama dengan NIK, bila belum diganti)_ |

### Menjalankan notifikasi terjadwal

Jalankan manual:

```bash
php spark notifikasi:kirim
```

Agar berjalan otomatis setiap hari, daftarkan di cron (Linux) atau Task Scheduler (Windows). Contoh cron, setiap hari pukul 07.00:

```
0 7 * * * cd /path/ke/proyek && php spark notifikasi:kirim
```

## Struktur Penting

| File | Fungsi |
|---|---|
| `app/Helpers/zscore_helper.php` | Perhitungan Z-Score dan status gizi |
| `app/Models/PengukuranModel.php` | Penentuan otomatis N/T/O/B |
| `app/Libraries/AkunOrangTua.php` | Pembuatan akun orang tua otomatis |
| `app/Libraries/NotifikasiService.php` | Logika notifikasi dan alert pertumbuhan |
| `app/Commands/KirimNotifikasi.php` | Perintah `spark` untuk notifikasi terjadwal |

## Diagram Use Case

_(Tambahkan gambar diagram use case di sini, mis. `docs/usecase.png`)_

## Lisensi

Dibangun di atas [CodeIgniter 4](https://codeigniter.com) (lisensi MIT).

## Penulis

**[Iqbal Bryan]** — [LinkedIn](https://www.linkedin.com/in/iqbal-bryan-b2678b434)
