<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($artikel['judul'] ?? 'Artikel') ?> - Posyandu Nusa Indah</title>
    <meta name="description" content="<?= esc(mb_strimwidth(trim(strip_tags($artikel['konten'] ?? '')), 0, 150, '...'), 'attr') ?>">
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

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .navbar-brand img { height: 45px; }

        .navbar-brand span {
            font-weight: 700;
            color: var(--biru-tua);
            font-size: 1.1rem;
        }

        .btn-login {
            background-color: white;
            border: 2px solid var(--biru-muda);
            color: var(--biru-tua);
            font-weight: 600;
            border-radius: 25px;
            padding: 6px 20px;
        }

        .btn-login:hover { background-color: var(--biru-muda); color: white; }

        .btn-daftar {
            background-color: var(--biru-muda);
            border: 2px solid var(--biru-muda);
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 6px 20px;
        }

        .btn-daftar:hover { background-color: var(--biru-tua); border-color: var(--biru-tua); color: white; }

        .artikel-header {
            background: linear-gradient(135deg, #EBF6FF 0%, #C8EAFB 100%);
            padding: 50px 0;
        }

        .badge-artikel {
            background-color: var(--biru-muda);
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .artikel-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--biru-tua);
        }

        .artikel-konten {
            font-size: 1.05rem;
            line-height: 1.9;
            color: #333;
        }

        .artikel-konten img { max-width: 100%; height: auto; border-radius: 10px; margin: 10px 0; }
        .artikel-konten h1, .artikel-konten h2, .artikel-konten h3 { color: var(--biru-tua); font-weight: 700; margin-top: 1.2rem; }
        .artikel-konten .ql-align-center { text-align: center; }
        .artikel-konten .ql-align-right { text-align: right; }
        .artikel-konten .ql-align-justify { text-align: justify; }

        .card-lainnya {
            text-decoration: none;
            color: inherit;
            display: block;
            background: var(--biru-bg);
            border-radius: 12px;
            padding: 16px;
            height: 100%;
            transition: transform 0.2s;
        }

        .card-lainnya:hover { transform: translateY(-4px); }

        .footer {
            background-color: var(--biru-tua);
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        .footer p { margin: 0; font-size: 0.9rem; opacity: 0.8; }
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
                <li class="nav-item"><a class="nav-link" href="/#artikel">Artikel</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0 ms-lg-3">
                <a href="/login" class="btn btn-login">Login</a>
                <a href="/register" class="btn btn-daftar">Daftar</a>
            </div>
        </div>
    </div>
</nav>

<!-- HEADER ARTIKEL -->
<section class="artikel-header">
    <div class="container">
        <a href="/#artikel" class="text-decoration-none" style="color: var(--biru-tua); font-weight:600;"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        <div class="mt-3">
            <span class="badge-artikel"><?= esc($artikel['kategori'] ?? 'Edukasi') ?></span>
        </div>
        <h1 class="artikel-title mt-3"><?= esc($artikel['judul'] ?? '') ?></h1>
        <div class="text-muted mt-2"><i class="bi bi-calendar3 me-1"></i><?= esc(!empty($artikel['created_at']) ? tglIndo($artikel['created_at']) : '-') ?></div>
    </div>
</section>

<!-- ISI ARTIKEL -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="artikel-konten"><?= nl2br($artikel['konten'] ?? '') ?></div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($lainnya)): ?>
<!-- ARTIKEL LAINNYA -->
<section class="py-5" style="background-color: var(--biru-bg);">
    <div class="container">
        <h4 class="fw-bold mb-4" style="color: var(--biru-tua)">Artikel Lainnya</h4>
        <div class="row g-4">
            <?php foreach ($lainnya as $l): ?>
            <div class="col-md-4">
                <a href="/artikel/<?= esc($l['id'], 'url') ?>" class="card-lainnya" style="background: white;">
                    <span class="badge-artikel"><?= esc($l['kategori'] ?? 'Edukasi') ?></span>
                    <h6 class="fw-bold mt-3" style="color: var(--biru-tua)"><?= esc($l['judul'] ?? '') ?></h6>
                    <small class="text-muted"><i class="bi bi-calendar3"></i> <?= esc(tglIndo($l['created_at'])) ?></small>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <img src="/assets/img/logo-posyandu.png" alt="Logo" style="height: 40px; margin-bottom: 10px;">
        <p>&copy; <?= esc(date('Y')) ?> Posyandu Nusa Indah. Semua hak dilindungi.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
