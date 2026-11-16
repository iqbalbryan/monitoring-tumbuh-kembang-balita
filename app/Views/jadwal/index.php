<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Jadwal Posyandu</h4>
        <p>Kelola jadwal kegiatan posyandu</p>
    </div>
    <a href="/admin/jadwal/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php
// Kartu jadwal. Tombol Edit hanya muncul untuk jadwal yang belum selesai.
$kartuJadwal = function (array $j, bool $selesai) {
    $warna = $selesai ? '#6c757d' : '#5BC8F5';
?>
    <div class="col-md-4">
        <div class="card h-100" style="border-left: 4px solid <?= esc($warna, 'attr') ?>;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge" style="background-color: <?= esc($warna, 'attr') ?>">
                        <?= esc($selesai ? 'Selesai' : 'Mendatang') ?>
                    </span>
                    <?php if (!$selesai): ?>
                        <a href="/admin/jadwal/edit/<?= esc($j['id'], 'url') ?>" class="btn btn-warning btn-sm">Edit</a>
                    <?php endif; ?>
                </div>
                <h6 class="fw-bold" style="color:#1A3A6B"><?= esc($j['judul']) ?></h6>
                <div class="text-muted small mb-1">
                    <i class="bi bi-calendar3"></i>
                    <?= esc(tglIndo($j['tanggal'])) ?>
                </div>
                <div class="text-muted small mb-1">
                    <i class="bi bi-clock"></i>
                    <?= esc(date('H:i', strtotime($j['waktu_mulai']))) ?> - <?= esc(date('H:i', strtotime($j['waktu_selesai']))) ?> WIB
                </div>
                <div class="text-muted small mb-2">
                    <i class="bi bi-geo-alt"></i>
                    <?= esc($j['lokasi']) ?>
                </div>
                <?php if (!empty($j['keterangan'])): ?>
                    <div class="small text-muted border-top pt-2">
                        <?= nl2br(esc($j['keterangan'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php }; ?>

<?php if (empty($mendatang) && empty($riwayat)): ?>
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
            <p class="mt-2">Belum ada jadwal posyandu</p>
            <a href="/admin/jadwal/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
        </div>
    </div>
<?php else: ?>

    <!-- JADWAL MENDATANG -->
    <h6 class="fw-bold mb-3" style="color:#1A3A6B"><i class="bi bi-calendar-event"></i> Jadwal Mendatang</h6>
    <div class="row g-4 mb-4">
        <?php if (!empty($mendatang)): ?>
            <?php foreach ($mendatang as $j) { $kartuJadwal($j, false); } ?>
        <?php else: ?>
            <div class="col-12"><div class="text-muted small">Tidak ada jadwal mendatang.</div></div>
        <?php endif; ?>
    </div>

    <!-- RIWAYAT PELAKSANAAN -->
    <?php if (!empty($riwayat)): ?>
        <h6 class="fw-bold mb-3" style="color:#1A3A6B"><i class="bi bi-clock-history"></i> Riwayat Pelaksanaan Posyandu</h6>
        <div class="row g-4 mb-4">
            <?php foreach ($riwayat as $j) { $kartuJadwal($j, true); } ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>