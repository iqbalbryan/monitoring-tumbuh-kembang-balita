<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4>Detail User</h4>
        <p>Informasi akun orang tua &amp; anak terkait</p>
    </div>
    <a href="/admin/users" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-person-badge"></i> Akun</h6></div>
            <div class="card-body">
                <?php $st = $user['status'] ?? ''; ?>
                <table class="table table-sm">
                    <tr><th style="width:130px;">Nama</th><td><?= esc($user['nama']) ?></td></tr>
                    <tr><th>NIK</th><td><?= esc($user['nik'] ?? '-') ?></td></tr>
                    <tr><th>Email</th><td><?= esc($user['email']) ?></td></tr>
                    <tr><th>Tanggal Daftar</th><td><?= esc(date('d-m-Y H:i', strtotime($user['created_at']))) ?></td></tr>
                    <tr><th>Status</th><td>
                        <?php if ($st === 'aktif'): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td></tr>
                </table>
                <?php if ($st === 'aktif'): ?>
                    <form action="/admin/users/toggle/<?= esc($user['id'], 'url') ?>" method="post" onsubmit="return confirm('Nonaktifkan akun ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-person-x"></i> Nonaktifkan Akun
                        </button>
                    </form>
                <?php else: ?>
                    <form action="/admin/users/toggle/<?= esc($user['id'], 'url') ?>" method="post" onsubmit="return confirm('Aktifkan akun ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-person-check"></i> Aktifkan Akun
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-people"></i> Anak Terkait (<?= esc(count($anak)) ?>)</h6></div>
            <div class="card-body">
                <?php if (!empty($anak)): ?>
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Anak</th>
                            <th>NIK</th>
                            <th>JK</th>
                            <th>Keaktifan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; foreach ($anak as $a): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><a href="/anak/detail/<?= esc($a['id'], 'url') ?>" class="text-decoration-none fw-semibold"><?= esc($a['nama']) ?></a></td>
                            <td><?= esc($a['nik_anak'] ?? '-') ?></td>
                            <td><?= esc(($a['jenis_kelamin'] ?? '') === 'Perempuan' ? 'P' : 'L') ?></td>
                            <td><?= esc($a['status_keaktifan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada anak yang terhubung dengan NIK orang tua ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
