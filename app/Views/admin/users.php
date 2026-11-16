<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h4>Manajemen User</h4>
    <p>Kelola akun orang tua yang mendaftar</p>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-people-fill"></i> Daftar User</h6>
        <form class="d-flex" method="get" action="/admin/users" style="max-width:340px;">
            <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Cari nama / NIK / email..." value="<?= esc($q ?? '', 'attr') ?>">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
            <?php if (!empty($q)): ?>
                <a href="/admin/users" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (!empty($q)): ?>
            <p class="text-muted small">Hasil pencarian untuk "<strong><?= esc($q) ?></strong>": <?= esc(count($users)) ?> user</p>
        <?php endif; ?>

        <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php $no = 1; foreach ($users as $user): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($user['nama']) ?></td>
                        <td><?= esc($user['nik'] ?? '-') ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc(date('d-m-Y H:i', strtotime($user['created_at']))) ?></td>
                        <td>
                            <?php if (($user['status'] ?? '') === 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/users/detail/<?= esc($user['id'], 'url') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Detail</a>
                            <?php if (($user['status'] ?? '') === 'aktif'): ?>
                                <form action="/admin/users/toggle/<?= esc($user['id'], 'url') ?>" method="post" class="d-inline" onsubmit="return confirm('Nonaktifkan akun <?= esc($user['nama']) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-person-x"></i> Nonaktifkan
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="/admin/users/toggle/<?= esc($user['id'], 'url') ?>" method="post" class="d-inline" onsubmit="return confirm('Aktifkan akun <?= esc($user['nama']) ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-person-check"></i> Aktifkan
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada user terdaftar</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
