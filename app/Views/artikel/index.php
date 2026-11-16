<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Artikel & Edukasi</h4>
        <p>Kelola artikel dan informasi kesehatan untuk orang tua</p>
    </div>
    <a href="/admin/artikel/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Artikel</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<div class="row g-4">
    <?php if (!empty($artikel)): ?>
        <?php foreach ($artikel as $a): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <?php
                        $warnakategori = [
                            'Gizi'            => 'bg-success',
                            'Tumbuh Kembang'  => 'bg-primary',
                            'Imunisasi'       => 'bg-warning text-dark',
                            'Kesehatan'       => 'bg-info',
                            'Lainnya'         => 'bg-secondary',
                        ];
                        ?>
                        <span class="badge <?= esc($warnakategori[$a['kategori']] ?? 'bg-secondary', 'attr') ?>">
                            <?= esc($a['kategori']) ?>
                        </span>
                        <span class="badge <?= esc($a['status'] === 'publikasi' ? 'bg-success' : 'bg-secondary', 'attr') ?>">
                            <?= esc(ucfirst($a['status'])) ?>
                        </span>
                    </div>
                    <h6 class="fw-bold mt-2" style="color:#1A3A6B"><?= esc($a['judul']) ?></h6>
                    <p class="text-muted small">
                        <?= esc(substr(strip_tags($a['konten']), 0, 100)) ?>...
                    </p>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-calendar3"></i>
                        <?= esc(tglIndo($a['created_at'])) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/admin/artikel/edit/<?= esc($a['id'], 'url') ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="/admin/artikel/delete/<?= esc($a['id'], 'url') ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus artikel ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-newspaper" style="font-size:3rem;"></i>
                    <p class="mt-2">Belum ada artikel</p>
                    <a href="/admin/artikel/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Artikel</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>