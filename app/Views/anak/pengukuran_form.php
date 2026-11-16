<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --biru-muda: #5BC8F5; --biru-tua: #1A3A6B; --biru-bg: #EBF6FF; }
        body { background-color: var(--biru-bg); font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: var(--biru-tua); }
        .navbar-brand, .navbar-text { color: white !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/admin/dashboard">
            <img src="/assets/img/logo-posyandu.png" height="35" alt="Logo">
            <span>Posyandu Nusa Indah</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="navbar-text"><i class="bi bi-person-circle"></i> <?= session()->get('user_nama') ?></span>
            <form action="/logout" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-light">Logout</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white rounded-top-4 py-3">
            <h5 class="mb-0 fw-bold" style="color: var(--biru-tua)">
                Tambah Pengukuran — <?= $anak['nama'] ?>
            </h5>
        </div>
        <div class="card-body">
            <form action="/anak/pengukuran/<?= $anak['id'] ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Ukur</label>
                        <input type="date" name="tanggal_ukur" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" class="form-control" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lingkar Kepala (cm)</label>
                        <input type="number" name="lingkar_kepala" class="form-control" step="0.01">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/anak/detail/<?= $anak['id'] ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengukuran</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>