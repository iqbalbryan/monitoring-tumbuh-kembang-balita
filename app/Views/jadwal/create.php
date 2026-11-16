<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h4>Tambah Jadwal Posyandu</h4>
    <p>Tambahkan jadwal kegiatan posyandu baru</p>
</div>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-body">
        <form action="/admin/jadwal/store" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" value="<?= old('judul') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control" value="<?= old('tanggal') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control" value="<?= old('waktu_mulai') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Waktu Selesai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_selesai" class="form-control" value="<?= old('waktu_selesai') ?>" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                    <input type="text" name="lokasi" class="form-control" value="<?= old('lokasi') ?>" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea name="keterangan" class="form-control"><?= old('keterangan') ?></textarea>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/jadwal" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>