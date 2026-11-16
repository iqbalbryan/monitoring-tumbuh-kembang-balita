<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posyandu Nusa Indah</title>
    <meta name="description" content="Posyandu Nusa Indah - layanan pemantauan tumbuh kembang balita, imunisasi, dan gizi. Lihat jadwal posyandu dan artikel kesehatan untuk keluarga.">
    <link rel="icon" href="/assets/img/logo-posyandu.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --biru-muda: #5BC8F5;
            --biru-tua: #1A3A6B;
            --biru-bg: #EBF6FF;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
        }

        /* NAVBAR */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .navbar-brand img {
            height: 45px;
        }

        .navbar-brand span {
            font-weight: 700;
            color: var(--biru-tua);
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link {
            color: var(--biru-tua);
            font-weight: 600;
            margin: 0 4px;
        }

        .navbar-nav .nav-link:hover {
            color: var(--biru-muda);
        }

        .btn-login {
            background-color: white;
            border: 2px solid var(--biru-muda);
            color: var(--biru-tua);
            font-weight: 600;
            border-radius: 25px;
            padding: 6px 20px;
        }

        .btn-login:hover {
            background-color: var(--biru-muda);
            color: white;
        }

        .btn-daftar {
            background-color: var(--biru-muda);
            border: 2px solid var(--biru-muda);
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 6px 20px;
        }

        .btn-daftar:hover {
            background-color: var(--biru-tua);
            border-color: var(--biru-tua);
            color: white;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #EBF6FF 0%, #C8EAFB 100%);
            padding: 80px 0;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--biru-tua);
        }

        .hero p {
            font-size: 1.1rem;
            color: #555;
        }

        .hero img {
            max-width: 420px;
            filter: drop-shadow(0px 14px 26px rgba(91,200,245,0.22));
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--biru-tua);
            margin-bottom: 10px;
        }

        .section-divider {
            width: 60px;
            height: 4px;
            background-color: var(--biru-muda);
            border-radius: 10px;
            margin: 0 auto 30px auto;
        }

        /* TENTANG POSYANDU */
        .tentang {
            padding: 60px 0;
            background-color: white;
        }

        .tentang .card {
            border: none;
            border-radius: 15px;
            background-color: var(--biru-bg);
            padding: 20px;
        }

        .tentang .icon-box {
            width: 60px;
            height: 60px;
            background-color: var(--biru-muda);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
        }

        /* TENTANG WEB */
        .tentang-web {
            padding: 60px 0;
            background-color: var(--biru-bg);
        }

        .tentang-web .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tentang-web .feature-icon {
            width: 45px;
            height: 45px;
            background-color: var(--biru-muda);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* WHY - ilustrasi hati */
        .why-heart {
            position: relative;
            display: inline-block;
            width: 230px;
            height: 230px;
        }

        .why-heart .heart-bg {
            font-size: 230px;
            line-height: 1;
            color: #FF8FA3;
            filter: drop-shadow(0 12px 22px rgba(255,107,138,0.3));
        }

        .why-heart .heart-people {
            position: absolute;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 96px;
            color: #ffffff;
        }

        /* JADWAL MENDATANG */
        .jadwal-sec {
            padding: 60px 0;
            background-color: white;
        }

        .jadwal-card2 {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            background: white;
            border-radius: 15px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            border-left: 5px solid var(--biru-muda);
        }

        .jadwal-date {
            background: var(--biru-muda);
            color: white;
            border-radius: 12px;
            padding: 10px 12px;
            text-align: center;
            min-width: 68px;
            flex-shrink: 0;
        }

        .jadwal-date .d {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
        }

        .jadwal-date .m {
            font-size: 0.72rem;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ARTIKEL */
        .artikel {
            padding: 60px 0;
            background-color: var(--biru-bg);
        }

        .artikel .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .artikel .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            height: 100%;
        }

        .artikel .card-link:hover .card {
            transform: translateY(-5px);
        }

        .artikel .card-img-top {
            border-radius: 15px 15px 0 0;
            height: 180px;
            object-fit: cover;
        }

        .artikel .badge-artikel {
            background-color: var(--biru-muda);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .artikel .card-title {
            font-weight: 700;
            color: var(--biru-tua);
            font-size: 1rem;
        }

        /* FOOTER */
        .footer {
            background-color: var(--biru-tua);
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <img src="/assets/img/logo-posyandu.png" alt="Logo Posyandu Nusa Indah">
            <span>Posyandu Nusa Indah</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#kenapa">Kenapa</a></li>
                <li class="nav-item"><a class="nav-link" href="#jadwal">Jadwal</a></li>
                <li class="nav-item"><a class="nav-link" href="#artikel">Artikel</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0 ms-lg-3">
                <a href="/login" class="btn btn-login">Login</a>
                <!-- <a href="/register" class="btn btn-daftar">Daftar</a> -->
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1>Selamat Datang di<br>Posyandu Nusa Indah &#128075;</h1>
                <p class="mt-3">Posyandu Nusa Indah hadir untuk memantau tumbuh kembang balita dengan pelayanan kesehatan yang ramah, mudah, dan terpercaya untuk seluruh keluarga.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="/login" class="btn btn-daftar px-4 py-2">Login Sekarang</a>
                    <!-- <a href="/register" class="btn btn-login px-4 py-2">Daftar Akun</a> -->
                </div>
            </div>
            <div class="col-md-5 text-center mt-4 mt-md-0">
                <img src="/assets/img/hero-anak.svg" alt="Ilustrasi pemantauan tumbuh kembang anak" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- TENTANG POSYANDU -->
<section class="tentang" id="tentang">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Tentang Posyandu Nusa Indah</h2>
            <div class="section-divider"></div>
        </div>
        <p class="text-center text-muted mb-4">Posyandu Nusa Indah adalah pusat layanan kesehatan masyarakat yang berfokus pada pemantauan tumbuh kembang balita, imunisasi, dan pemberian gizi untuk generasi yang sehat dan kuat.</p>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <div class="icon-box"><i class="bi bi-heart-pulse"></i></div>
                    <h5 class="fw-bold" style="color: var(--biru-tua)">Pemantauan Kesehatan</h5>
                    <p class="text-muted">Memantau berat badan, tinggi badan, dan perkembangan balita secara rutin setiap bulan.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <div class="icon-box"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold" style="color: var(--biru-tua)">Riwayat Pertumbuhan</h5>
                    <p class="text-muted">Menyimpan riwayat hasil pengukuran balita sehingga perkembangan anak dapat dipantau dari waktu ke waktu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <div class="icon-box"><i class="bi bi-egg-fried"></i></div>
                    <h5 class="fw-bold" style="color: var(--biru-tua)">Pemberian Gizi</h5>
                    <p class="text-muted">Memberikan edukasi dan suplemen gizi untuk mendukung tumbuh kembang balita yang optimal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KENAPA MENGGUNAKAN APLIKASI (WHY) -->
<section class="tentang-web" id="kenapa">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Kenapa Menggunakan Aplikasi Ini?</h2>
            <div class="section-divider"></div>
        </div>
        <p class="text-center text-muted mb-5">Aplikasi web Posyandu Nusa Indah dirancang untuk memudahkan orang tua memantau tumbuh kembang anak secara digital, cepat, dan akurat.</p>
        <div class="row align-items-center g-4">
            <div class="col-md-5 order-md-2 text-center mb-4 mb-md-0">
                <div class="why-heart">
                    <i class="bi bi-heart-fill heart-bg"></i>
                    <i class="bi bi-people-fill heart-people"></i>
                </div>
                <h5 class="fw-bold mt-3" style="color: var(--biru-tua)">Sehat &amp; Tumbuh Bersama Keluarga</h5>
            </div>
            <div class="col-md-7 order-md-1">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--biru-tua)">Pantau Tumbuh Kembang</h6>
                        <p class="text-muted mb-0">Lihat grafik berat &amp; tinggi badan anak dari waktu ke waktu.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-bell"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--biru-tua)">Notifikasi &amp; Pengingat</h6>
                        <p class="text-muted mb-0">Dapatkan pengingat jadwal posyandu dan informasi penting lainnya.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--biru-tua)">Data Aman &amp; Terlindungi</h6>
                        <p class="text-muted mb-0">Data keluarga dilindungi dengan sistem login dan autentikasi yang aman.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JADWAL MENDATANG -->
<section class="jadwal-sec" id="jadwal">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Jadwal Posyandu Mendatang</h2>
            <div class="section-divider"></div>
        </div>
        <?php if (!empty($jadwalMendatang)): ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($jadwalMendatang as $j): ?>
            <?php $ts = strtotime($j['tanggal']); ?>
            <div class="col-md-4">
                <div class="jadwal-card2">
                    <div class="jadwal-date">
                        <div class="d"><?= date('d', $ts) ?></div>
                        <div class="m"><?= esc(blnSingkatIndo($ts)) ?></div>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--biru-tua)"><?= esc($j['judul'] ?? 'Kegiatan Posyandu') ?></h6>
                        <div class="text-muted small mb-1"><i class="bi bi-calendar3 me-1"></i><?= esc(tglIndo($j['tanggal'])) ?></div>
                        <?php if (!empty($j['waktu_mulai'])): ?>
                        <div class="text-muted small mb-1"><i class="bi bi-clock me-1"></i><?= esc(substr($j['waktu_mulai'], 0, 5)) ?><?= !empty($j['waktu_selesai']) ? ' - ' . esc(substr($j['waktu_selesai'], 0, 5)) . ' WIB' : ' WIB' ?></div>
                        <?php endif; ?>
                        <?php if (!empty($j['lokasi'])): ?>
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i><?= esc($j['lokasi']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="bi bi-calendar-x" style="font-size: 2.5rem; color: var(--biru-muda);"></i>
            <p class="mt-3 mb-0">Belum ada jadwal posyandu yang akan datang. Silakan cek kembali nanti.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ARTIKEL -->
<section class="artikel" id="artikel">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Artikel Kesehatan</h2>
            <div class="section-divider"></div>
        </div>
        <?php if (!empty($artikelTerbaru)): ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($artikelTerbaru as $a): ?>
            <?php $ringkas = trim(preg_replace('/\s+/', ' ', strip_tags(str_replace('<', ' <', $a['konten'] ?? '')))); ?>
            <div class="col-md-4">
                <a href="/artikel/<?= $a['id'] ?>" class="card-link">
                    <div class="card">
                        <div class="card-body p-4">
                            <span class="badge-artikel"><?= esc($a['kategori'] ?? 'Edukasi') ?></span>
                            <h5 class="card-title mt-3"><?= esc($a['judul'] ?? '') ?></h5>
                            <p class="text-muted" style="font-size: 0.9rem;"><?= esc(mb_strimwidth($ringkas, 0, 120, '...')) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted"><i class="bi bi-calendar3"></i> <?= esc(tglIndo($a['created_at'])) ?></small>
                                <small class="fw-semibold" style="color: var(--biru-muda)">Selengkapnya <i class="bi bi-arrow-right"></i></small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="bi bi-journal-text" style="font-size: 2.5rem; color: var(--biru-muda);"></i>
            <p class="mt-3 mb-0">Belum ada artikel yang dipublikasikan.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <img src="/assets/img/logo-posyandu.png" alt="Logo" style="height: 40px; margin-bottom: 10px;">
        <p>&copy; <?= date('Y') ?> Posyandu Nusa Indah. Semua hak dilindungi.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
