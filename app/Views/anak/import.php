<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h4>Import Data Anak</h4>
    <p>Upload file Excel untuk import data anak secara massal</p>
</div>

<div class="card">
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <p>Format kolom Excel yang diperlukan:</p>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>A (No)</th>
                        <th>B (NIK Anak)</th>
                        <th>C (Nama)</th>
                        <th>D (JK)</th>
                        <th>E (Tgl Lahir)</th>
                        <th>F (BB Lahir)</th>
                        <th>G (PB Lahir)</th>
                        <th>H (LK Lahir)</th>
                        <th>I (Premature)</th>
                        <th>J (No KK)</th>
                        <th>K (NIK Ibu)</th>
                        <th>L (Nama Ibu)</th>
                        <th>M (NIK Ayah)</th>
                        <th>N (Nama Ayah)</th>
                    </tr>
                </thead>
            </table>
        </div>

        <form action="/anak/import" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Pilih File Excel</label>
                <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <a href="/anak" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Import</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>