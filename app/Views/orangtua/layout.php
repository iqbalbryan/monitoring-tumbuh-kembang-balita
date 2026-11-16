<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? ($title ?? 'Orang Tua')) ?> - Posyandu Nusa Indah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <?= $this->renderSection('head') ?>
    <style>
        :root{
            --pos-primary:#2563a8;
            --pos-primary-dark:#1a3a6b;
            --pos-primary-soft:#e7f0fb;
            --pos-accent:#5bc8f5;
            --pos-bg:#eef5fc;
            --pos-ink:#1e2a3a;
            --pos-muted:#6b7a90;
            --sidebar-bg:#1a3a6b;
            --sidebar-active:#2e5aa0;
            --pos-card-radius:16px;
            --pos-shadow:0 4px 18px rgba(26,58,107,.08);
            --pos-shadow-hover:0 8px 28px rgba(26,58,107,.16);
            --sidebar-w:248px;
        }
        *{font-family:'Segoe UI',system-ui,-apple-system,sans-serif;}
        body{background:var(--pos-bg);color:var(--pos-ink);margin:0;}
        a{text-decoration:none;}

        .pos-layout{display:flex;min-height:100vh;}

        /* Sidebar */
        .pos-sidebar{width:var(--sidebar-w);background:var(--sidebar-bg);border-right:1px solid rgba(255,255,255,.06);position:fixed;top:0;bottom:0;left:0;display:flex;flex-direction:column;padding:0;z-index:1030;overflow-y:auto;scrollbar-gutter:stable;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.25) transparent;}
        .pos-sidebar::-webkit-scrollbar{width:6px;}
        .pos-sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.25);border-radius:3px;}
        .pos-sidebar::-webkit-scrollbar-track{background:transparent;}
        .pos-brand{font-weight:700;color:#fff;letter-spacing:-.3px;font-size:1.02rem;display:flex;align-items:center;gap:10px;padding:20px 16px;border-bottom:1px solid rgba(255,255,255,.1);text-decoration:none;}
        .pos-brand img{height:30px;}
        .pos-nav{padding:16px 0;flex:1;}
        .pos-nav-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.4);padding:8px 20px 4px;}
        .pos-nav a{display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,.7);text-decoration:none;font-size:.9rem;font-weight:500;transition:all .2s;position:relative;border-left:3px solid transparent;}
        .pos-nav a i{font-size:1.1rem;color:inherit;width:20px;text-align:center;}
        .pos-nav a:hover{background:rgba(255,255,255,.08);color:#fff;border-left-color:var(--pos-accent);}
        .pos-nav a:hover i{color:#fff;}
        .pos-nav a.active{background:rgba(46,90,160,.45);color:#fff;border-left-color:var(--pos-accent);font-weight:600;}
        .pos-nav a.active i{color:#fff;}
        .pos-nav .nav-badge{position:absolute;right:16px;background:#e74c3c;color:#fff;border-radius:999px;font-size:.68rem;padding:1px 7px;font-weight:700;}
        .pos-nav-dropdown-toggle{cursor:pointer;}
        .pos-nav-dropdown-toggle .pos-nav-caret{margin-left:auto;font-size:.7rem;width:auto !important;transition:transform .2s;}
        .pos-nav-dropdown-toggle[aria-expanded="true"] .pos-nav-caret{transform:rotate(180deg);}
        .pos-nav-sub-wrap{background:rgba(0,0,0,.12);}
        .pos-nav-sub-wrap a{padding-left:52px;font-size:.84rem;}
        .pos-sidebar-foot{margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding:16px;}
        .pos-user-info{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
        .pos-avatar{width:36px;height:36px;border-radius:50%;background:var(--pos-accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;flex:0 0 auto;}
        .pos-btn-logout{display:block;width:100%;background:rgba(255,255,255,.1);color:#fff;border:none;border-radius:8px;padding:8px;text-align:center;text-decoration:none;font-size:.85rem;transition:background .2s;border: none;cursor: pointer;}
        .pos-btn-logout:hover{background:rgba(220,53,69,.7);color:#fff;}

        /* Main */
        .pos-main{flex:1;margin-left:var(--sidebar-w);display:flex;flex-direction:column;min-width:0;}
        .pos-topbar{background:#fff;border-bottom:1px solid #e2ebf5;padding:14px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:1020;}
        .pos-content{padding:24px 28px;}
        .pos-burger{display:none;background:none;border:none;font-size:1.4rem;color:var(--pos-ink);}
        /* Offset anchor agar judul tidak ketutup topbar sticky */
        html{scroll-behavior:smooth;}
        #ringkasan,#grafik,#kms,#riwayat,#jadwal,#artikel{scroll-margin-top:84px;}

        /* Cards */
        .pos-card{background:#fff;border:none;border-radius:var(--pos-card-radius);box-shadow:var(--pos-shadow);transition:.2s;}
        .pos-card:hover{box-shadow:var(--pos-shadow-hover);}
        .pos-card .card-body{padding:20px;}
        .pos-section-title{font-weight:700;font-size:1.02rem;margin:0;display:flex;align-items:center;gap:8px;color:var(--pos-ink);}
        .pos-section-title i{color:var(--pos-primary);}

        .pos-badge{font-weight:600;border-radius:999px;padding:4px 11px;font-size:.74rem;}

        .pos-list-item{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid #eef2f7;}
        .pos-list-item:last-child{border-bottom:none;}
        .pos-list-ico{width:40px;height:40px;border-radius:11px;flex:0 0 auto;display:flex;align-items:center;justify-content:center;font-size:1.1rem;background:var(--pos-primary-soft);color:var(--pos-primary);}

        .pos-empty{text-align:center;padding:40px 20px;color:var(--pos-muted);}
        .pos-empty .ico{font-size:3rem;color:var(--pos-primary);}

        .chart-wrap{position:relative;height:300px;}
        .kms-wrap{position:relative;height:340px;}

        .pos-overlay{display:none;}
        @media(max-width:991px){
            .pos-sidebar{transform:translateX(-100%);transition:transform .25s;}
            .pos-sidebar.open{transform:translateX(0);}
            .pos-main{margin-left:0;}
            .pos-burger{display:inline-block;}
            .pos-overlay.show{display:block;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1025;}
        }
    </style>
</head>
<body>
<?php $am = $activeMenu ?? ''; $nc = $notifCount ?? 0; ?>
<div class="pos-layout">

    <!-- ===== SIDEBAR ===== -->
    <aside class="pos-sidebar" id="sidebar">
        <a class="pos-brand" href="/orangtua/dashboard">
            <img src="/assets/img/logo-posyandu.png" alt="Logo"> Posyandu Nusa Indah
        </a>
        <div class="pos-nav">
            <div class="pos-nav-label">Menu Utama</div>
            <a href="/orangtua/dashboard" class="<?= $am === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <div class="pos-nav-label">Rapor Anak</div>
            <a href="/orangtua/dashboard#ringkasan"><i class="bi bi-clipboard2-pulse"></i> Hasil Terakhir</a>
            <a href="#navGrafik" class="pos-nav-dropdown-toggle" data-bs-toggle="collapse" role="button"
               aria-expanded="false" aria-controls="navGrafik">
                <i class="bi bi-graph-up-arrow"></i> Grafik
                <i class="bi bi-chevron-down pos-nav-caret"></i>
            </a>
            <div class="collapse pos-nav-sub-wrap" id="navGrafik">
                <a href="/orangtua/dashboard#grafik"><i class="bi bi-bar-chart-line"></i> Berat &amp; Tinggi Badan</a>
                <a href="/orangtua/dashboard#kms"><i class="bi bi-card-checklist"></i> BB/Umur (Underweight)</a>
                <a href="/orangtua/dashboard#kms-tb"><i class="bi bi-rulers"></i> TB/Umur (Stunting)</a>
                <a href="/orangtua/dashboard#kms-bbtb"><i class="bi bi-clipboard2-pulse"></i> BB/TB (Wasting)</a>
            </div>
            <a href="/orangtua/dashboard#riwayat"><i class="bi bi-table"></i> Riwayat Posyandu</a>
            <div class="pos-nav-label">Informasi</div>
            <a href="/orangtua/dashboard#jadwal"><i class="bi bi-calendar-event"></i> Jadwal Posyandu</a>
            <a href="/orangtua/dashboard#artikel"><i class="bi bi-book"></i> Artikel &amp; Edukasi</a>
            <a href="/orangtua/notifikasi" class="<?= $am === 'notifikasi' ? 'active' : '' ?>"><i class="bi bi-bell"></i> Notifikasi
                <?php if (!empty($nc)): ?><span class="nav-badge"><?= $nc ?></span><?php endif; ?>
            </a>
            <div class="pos-nav-label">Akun</div>
            <a href="/orangtua/ganti-password" class="<?= $am === 'password' ? 'active' : '' ?>"><i class="bi bi-key"></i> Ganti Password</a>
        </div>
        <div class="pos-sidebar-foot">
            <div class="pos-user-info">
                <div class="pos-avatar"><?= strtoupper(substr(session()->get('user_nama') ?? 'U', 0, 1)) ?></div>
                <div style="min-width:0">
                    <div class="fw-semibold text-truncate text-white" style="font-size:.85rem"><?= esc(session()->get('user_nama')) ?></div>
                    <div class="text-truncate" style="font-size:.75rem;color:rgba(255,255,255,.5)">Orang Tua</div>
                </div>
            </div>
            <form action="/logout" method="post" onsubmit="return confirm('Yakin ingin logout?')">
                <?= csrf_field() ?>
                <button type="submit" class="pos-btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </aside>
    <div class="pos-overlay" id="overlay"></div>

    <!-- ===== MAIN ===== -->
    <div class="pos-main">
        <header class="pos-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="pos-burger" id="burger"><i class="bi bi-list"></i></button>
                <div>
                    <div class="fw-bold" style="font-size:1.05rem"><?= esc($pageTitle ?? ($title ?? 'Orang Tua')) ?></div>
                    <div class="text-secondary" style="font-size:.82rem"><?= esc($pageSubtitle ?? ('Selamat datang, ' . session()->get('user_nama') . '!')) ?></div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="/orangtua/notifikasi" class="position-relative text-secondary fs-5">
                    <i class="bi bi-bell"></i>
                    <?php if (!empty($nc)): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.55rem"><?= $nc ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </header>

        <div class="pos-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert d-flex align-items-center gap-2 mb-3" style="background:#e3f5ee;border:none;border-radius:14px;color:#1f7a5f">
                    <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert d-flex align-items-center gap-2 mb-3" style="background:#fde4e1;border:none;border-radius:14px;color:#c0392b">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function(){
        var burger = document.getElementById('burger'),
            sb = document.getElementById('sidebar'),
            ov = document.getElementById('overlay');
        if (burger) burger.addEventListener('click', function(){ sb.classList.toggle('open'); ov.classList.toggle('show'); });
        if (ov) ov.addEventListener('click', function(){ sb.classList.remove('open'); ov.classList.remove('show'); });

        // Buka otomatis dropdown "Grafik" jika hash URL mengarah ke salah satu chart
        var grafikHashes = ['#grafik', '#kms', '#kms-tb', '#kms-bbtb'];
        if (grafikHashes.indexOf(window.location.hash) !== -1) {
            var navGrafik = document.getElementById('navGrafik');
            var toggle = document.querySelector('[data-bs-toggle="collapse"][href="#navGrafik"]');
            if (navGrafik) navGrafik.classList.add('show');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }
    })();
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
