<?= $this->extend('orangtua/layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="pos-card">
            <div class="card-body p-4">
                <h6 class="pos-section-title mb-1"><i class="bi bi-person-plus"></i> Tambah Data Anak</h6>
                <p class="text-secondary small mb-4">Masukkan NIK anak yang sudah terdaftar di Posyandu Nusa Indah</p>
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
                <form action="/orangtua/simpan-anak" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIK Anak <span class="text-danger">*</span></label>
                        <input type="text" name="nik_anak" class="form-control" value="<?= old('nik_anak') ?>" required>
                        <div class="form-text">NIK anak terdapat pada Kartu Keluarga atau KIA (Kartu Identitas Anak)</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/orangtua/dashboard" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn text-white" style="background:var(--pos-primary)"><i class="bi bi-search"></i> Cari &amp; Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
