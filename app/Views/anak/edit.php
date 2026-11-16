<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h4>Edit Data Anak</h4>
    <p>Ubah data anak yang sudah terdaftar</p>
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
        <form action="/anak/update/<?= $anak['id'] ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">NIK Anak</label>
                    <input type="text" name="nik_anak" class="form-control" value="<?= old('nik_anak', $anak['nik_anak']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Anak</label>
                    <input type="text" name="nama" class="form-control" value="<?= old('nama', $anak['nama']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" <?= old('jenis_kelamin', $anak['jenis_kelamin']) === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= old('jenis_kelamin', $anak['jenis_kelamin']) === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= old('tanggal_lahir', $anak['tanggal_lahir']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Berat Badan Lahir (kg)</label>
                    <input type="number" name="berat_badan_lahir" class="form-control" step="0.01" value="<?= old('berat_badan_lahir', $anak['berat_badan_lahir']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Panjang Badan Lahir (cm)</label>
                    <input type="number" name="panjang_badan_lahir" class="form-control" step="0.01" value="<?= old('panjang_badan_lahir', $anak['panjang_badan_lahir']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lingkar Kepala Lahir (cm)</label>
                    <input type="number" name="lingkar_kepala_lahir" class="form-control" step="0.01" value="<?= old('lingkar_kepala_lahir', $anak['lingkar_kepala_lahir']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Lahir</label>
                    <select name="premature" class="form-control">
                        <option value="Mature" <?= old('premature', $anak['premature']) === 'Mature' ? 'selected' : '' ?>>Mature</option>
                        <option value="Premature" <?= old('premature', $anak['premature']) === 'Premature' ? 'selected' : '' ?>>Premature</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No KK</label>
                    <input type="text" name="no_kk" class="form-control" value="<?= old('no_kk', $anak['no_kk']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ibu</label>
                    <input type="text" name="nik_ibu" class="form-control" value="<?= old('nik_ibu', $anak['nik_ibu']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ibu</label>
                    <input type="text" name="nama_ibu" class="form-control" value="<?= old('nama_ibu', $anak['nama_ibu']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK Ayah</label>
                    <input type="text" name="nik_ayah" class="form-control" value="<?= old('nik_ayah', $anak['nik_ayah']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Ayah</label>
                    <input type="text" name="nama_ayah" class="form-control" value="<?= old('nama_ayah', $anak['nama_ayah']) ?>">
                </div>
            </div>
            <div class="mt-4">
                <a href="/anak" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>