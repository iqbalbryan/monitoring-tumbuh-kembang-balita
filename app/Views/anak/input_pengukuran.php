<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container .select2-selection--single {
        height: 45px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 6px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #1a1a1a;
    }
    .select2-dropdown {
        border-radius: 8px;
        border: 1px solid #ced4da;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>

<div class="page-header">
    <h4>Input Pengukuran</h4>
    <p>Input data pengukuran anak di posyandu</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
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
    <div class="card-header">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-clipboard-pulse"></i> Form Pengukuran</h6>
    </div>
    <div class="card-body">
        <form action="/anak/simpan-pengukuran" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Pengukuran</label>
                    <input type="text" class="form-control bg-light" value="<?= esc(date('d-m-Y', strtotime($tanggalHariIni ?? date('Y-m-d')))) ?>" disabled>
                    <!-- <div class="form-text">Terisi otomatis sesuai tanggal hari ini dan tidak dapat diubah.</div> -->
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Anak <span class="text-danger">*</span></label>
                    <select name="anak_id" id="anak_id" class="form-control" required style="width:100%">
                        <option value="">-- Cari nama anak --</option>
                        <?php foreach ($anak as $a): ?>
                            <option value="<?= $a['id'] ?>">
                                <?= $a['nama'] ?> — <?= $a['nik_anak'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Berat Badan (kg) <span class="text-danger">*</span></label>
                    <input type="number" name="berat_badan" class="form-control" step="0.01" value="<?= old('berat_badan') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Panjang/Tinggi Badan (cm) <span class="text-danger">*</span></label>
                    <input type="number" name="tinggi_badan" class="form-control" step="0.01" value="<?= old('tinggi_badan') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Lingkar Kepala (cm)</label>
                    <input type="number" name="lingkar_kepala" class="form-control" step="0.01" value="<?= old('lingkar_kepala') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">LiLA (cm)</label>
                    <input type="number" name="lila" class="form-control" step="0.01" value="<?= old('lila') ?>">
                </div>

                <!-- <div class="col-md-6">
                    <label class="form-label fw-semibold">Keterangan N/T/O/B</label>
                    <div class="form-control bg-light text-muted" style="height:auto">
                        <i class="bi bi-magic"></i> Ditentukan otomatis oleh sistem saat disimpan
                    </div>
                    <div class="form-text">
                        N = Naik, T = Tidak naik, O = Tidak ditimbang bulan lalu, B = Balita baru
                        (berdasarkan riwayat penimbangan anak).
                    </div>
                </div> -->

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control"><?= old('keterangan') ?></textarea>
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Simpan Pengukuran
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#anak_id').select2({
            placeholder: '-- Cari nama anak --',
            allowClear: true,
        });
    });
</script>

<?= $this->endSection() ?>