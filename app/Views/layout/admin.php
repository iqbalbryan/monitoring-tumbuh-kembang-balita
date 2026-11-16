<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Posyandu') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --biru-muda: #5BC8F5;
            --biru-tua: #1A3A6B;
            --biru-bg: #eef5fc;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--biru-bg);
            margin: 0;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--biru-tua);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header img {
            height: 40px;
        }

        .sidebar-header span {
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .sidebar-menu {
            padding: 16px 0;
            flex: 1;
        }

        .sidebar-label {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 20px 4px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-item:hover {
            background-color: rgba(255,255,255,0.08);
            color: white;
            border-left-color: var(--biru-muda);
        }

        .sidebar-item.active {
            background-color: rgba(46,90,160,0.45);
            color: white;
            border-left-color: var(--biru-muda);
            font-weight: 600;
        }

        .sidebar-item i {
            font-size: 1.1rem;
            width: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background-color: var(--biru-muda);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .user-name {
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-role {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }

        .btn-logout {
            display: block;
            width: 100%;
            background-color: rgba(255,255,255,0.1);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background-color: rgba(220,53,69,0.7);
            color: white;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 24px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h4 {
            font-weight: 700;
            color: var(--biru-tua);
            margin: 0;
        }

        .page-header p {
            color: #888;
            font-size: 0.85rem;
            margin: 4px 0 0;
        }

        /* CARD */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .card-header {
            background: white;
            border-radius: 16px 16px 0 0 !important;
            border-bottom: 1px solid #f0f0f0;
            padding: 16px 20px;
        }

        /* ===== Harmonisasi UI/UX (selaras dengan sisi Orang Tua) ===== */
        :root{
            --pos-primary:#2563a8;
            --pos-primary-dark:#1a3a6b;
            --pos-primary-soft:#e7f0fb;
            --pos-accent:#5bc8f5;
            --pos-ink:#1e2a3a;
            --pos-muted:#6b7a90;
            --pos-shadow:0 4px 18px rgba(26,58,107,.08);
            --pos-shadow-hover:0 8px 28px rgba(26,58,107,.16);
        }
        body{color:var(--pos-ink);}

        /* Kartu */
        .card{border:1px solid #eef2f7 !important;border-radius:16px !important;box-shadow:var(--pos-shadow) !important;transition:box-shadow .2s ease,transform .2s ease;}
        .card.h-100:hover{box-shadow:var(--pos-shadow-hover);transform:translateY(-2px);}
        .card-header{background:#fff !important;border-bottom:1px solid #eef2f7 !important;border-radius:16px 16px 0 0 !important;padding:16px 20px !important;}
        .card-header h6{color:var(--pos-primary-dark) !important;}

        /* Header halaman */
        .page-header h4{font-size:1.45rem;font-weight:800;color:var(--pos-primary-dark);letter-spacing:-.3px;}
        .page-header p{color:var(--pos-muted);font-size:.88rem;}

        /* Tombol */
        .btn{border-radius:10px;font-weight:600;}
        .btn-sm{border-radius:9px;}
        .btn-primary{background:var(--pos-primary);border-color:var(--pos-primary);}
        .btn-primary:hover,.btn-primary:focus{background:var(--pos-primary-dark);border-color:var(--pos-primary-dark);}
        .btn-outline-primary{color:var(--pos-primary);border-color:var(--pos-primary);}
        .btn-outline-primary:hover{background:var(--pos-primary);border-color:var(--pos-primary);color:#fff;}
        .btn-warning{background:#e6b24a;border-color:#e6b24a;color:#3a2e00;}
        .btn-warning:hover{background:#d7a235;border-color:#d7a235;color:#3a2e00;}
        .btn-danger{background:#e2574c;border-color:#e2574c;}
        .btn-danger:hover{background:#c8463c;border-color:#c8463c;}
        .btn-success{background:#1f9d57;border-color:#1f9d57;}
        .btn-success:hover{background:#198049;border-color:#198049;}
        .btn-secondary{background:#eef2f7;border-color:#eef2f7;color:#46566b;}
        .btn-secondary:hover{background:#dde6f0;border-color:#dde6f0;color:#2b3a4d;}

        /* Tabel — header lembut bernuansa biru (ganti header hitam table-dark) */
        .table{color:var(--pos-ink);vertical-align:middle;}
        .table thead th{background:var(--pos-primary-soft) !important;color:var(--pos-primary-dark) !important;border-bottom:2px solid #d7e3f4 !important;border-top:none !important;font-size:.78rem;text-transform:uppercase;letter-spacing:.4px;font-weight:700;padding:12px 14px;}
        .table.table-dark>thead>tr>th,.table>thead.table-dark>tr>th{background:var(--pos-primary-soft) !important;color:var(--pos-primary-dark) !important;--bs-table-bg:var(--pos-primary-soft);--bs-table-color:var(--pos-primary-dark);}
        .table td{padding:11px 14px;border-color:#eef2f7;}
        .table.table-bordered,.table.table-bordered td,.table.table-bordered th{border-color:#eef2f7 !important;}
        .table.table-striped>tbody>tr:nth-of-type(odd)>*{--bs-table-accent-bg:#f7faff;background-color:#f7faff;}
        .table-hover>tbody>tr:hover>*{--bs-table-accent-bg:var(--pos-primary-soft);background-color:var(--pos-primary-soft);}
        .table-responsive{border-radius:12px;overflow:hidden;}

        /* Badge — palet soft pastel (selaras dashboard) */
        .badge{font-weight:600;border-radius:8px;padding:.42em .68em;letter-spacing:.2px;}
        .badge.bg-primary{background:#EDF3FC !important;color:#2F5E96 !important;}
        .badge.bg-success{background:#EAF6F0 !important;color:#2C7D5E !important;}
        .badge.bg-danger{background:#FBECE8 !important;color:#BB5747 !important;}
        .badge.bg-warning{background:#FBF3E1 !important;color:#A9772A !important;}
        .badge.bg-info{background:#E9F4F7 !important;color:#2C7186 !important;}
        .badge.bg-secondary{background:#F0F2F6 !important;color:#6B7890 !important;}
        .badge.bg-light{background:#F0F2F6 !important;color:#6B7890 !important;}
        .badge.bg-dark{background:#E4EBF3 !important;color:#51617A !important;}
        /* Badge "alert" coral lembut untuk penanda perlu perhatian */
        .badge-alert{background:#E27B66 !important;color:#fff !important;}

        /* Form */
        .form-control,.form-select{border-radius:10px;border-color:#dde6f0;}
        .form-control:focus,.form-select:focus{border-color:var(--pos-accent);box-shadow:0 0 0 .2rem rgba(91,200,245,.25);}
        .form-label{color:var(--pos-primary-dark);}

        /* Alert */
        .alert{border-radius:12px;border:none;}
        .alert-success{background:#e3f5ee;color:#1f7a5f;}
        .alert-danger{background:#fdecea;color:#c0392b;}

        /* Sidebar — indikator aktif gaya pill (modern, selaras Orang Tua) */
        .sidebar-menu{padding:16px 10px;}
        .sidebar-label{padding:14px 12px 6px;}
        .sidebar-item{border-radius:11px;border-left:3px solid transparent;margin:2px 0;}
        .sidebar-item.active{background:var(--biru-tua);background:rgba(91,200,245,.16);border-left-color:var(--pos-accent);}
    </style>

    <?= $this->renderSection('head') ?>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="/assets/img/logo-posyandu.png" alt="Logo">
        <span>Posyandu<br>Nusa Indah</span>
    </div>

    <div class="sidebar-menu">
        <div class="sidebar-label">Menu Utama</div>
        <a href="/admin/dashboard" class="sidebar-item <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/anak" class="sidebar-item <?= (uri_string() === 'anak') ? 'active' : '' ?>">
            <i class="bi bi-heart-pulse"></i> Data Anak
        </a>
        <a href="/anak/input-pengukuran" class="sidebar-item <?= (uri_string() === 'anak/input-pengukuran') ? 'active' : '' ?>">
            <i class="bi bi-clipboard-pulse"></i> Input Pengukuran
        </a>
        <a href="/anak/hasil-pengukuran" class="sidebar-item <?= (uri_string() === 'anak/hasil-pengukuran') ? 'active' : '' ?>">
            <i class="bi bi-table"></i> Hasil Pengukuran
        </a>
        <!-- <a href="/anak/import-pengukuran" class="sidebar-item <?= (uri_string() === 'anak/import-pengukuran') ? 'active' : '' ?>">
            <i class="bi bi-upload"></i> Import Pengukuran
        </a> -->
        <div class="sidebar-label">Manajemen</div>
        <a href="/admin/users" class="sidebar-item <?= (uri_string() === 'admin/users') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Manajemen User
        </a>
        <a href="/admin/jadwal" class="sidebar-item <?= (uri_string() === 'admin/jadwal') ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i> Jadwal Posyandu
        </a>
        <a href="/admin/artikel" class="sidebar-item <?= (strpos(uri_string(), 'admin/artikel') !== false) ? 'active' : '' ?>">
            <i class="bi bi-newspaper"></i> Artikel & Edukasi
        </a>
        <a href="/admin/laporan" class="sidebar-item <?= (strpos(uri_string(), 'admin/laporan') !== false) ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Bulanan
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><i class="bi bi-person"></i></div>
            <div>
                <div class="user-name"><?= session()->get('user_nama') ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <form action="/logout" method="post" onsubmit="return confirm('Yakin ingin logout?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
        </a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>