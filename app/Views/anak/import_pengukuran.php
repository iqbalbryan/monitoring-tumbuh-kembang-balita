<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h4>Import Pengukuran Massal</h4>
    <p>Upload file Excel untuk mengimpor data pengukuran yang sudah ada sekaligus</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h6 class="fw-bold mb-1" style="color:#1A3A6B"><i class="bi bi-info-circle"></i> Petunjuk</h6>
                <p class="mb-0 text-muted">Anak dicocokkan berdasarkan <strong>NIK Anak</strong>. Z-Score (BB/U, PB/U atau TB/U, BB/TB) status gizi, dan N/T/O/B dihitung otomatis saat import.</p>
            </div>
            <a href="/anak/template-pengukuran" class="btn btn-outline-primary"><i class="bi bi-download"></i> Download Template</a>
        </div>

        <p class="fw-semibold mb-2">Format kolom Excel:</p>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>A (No)</th>
                        <th>B (NIK Anak) *</th>
                        <th>C (Tanggal Ukur) *</th>
                        <th>D (Berat Badan kg) *</th>
                        <th>E (Panjang/Tinggi Badan cm) *</th>
                        <th>F (Lingkar Kepala cm)</th>
                        <th>G (LiLA cm)</th>
                        <th>H (N/T/O/B) - otomatis</th>
                        <th>I (Keterangan)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>3402172208240003</td>
                        <td>2026-05-09</td>
                        <td>9.65</td>
                        <td>80.30</td>
                        <td>46.5</td>
                        <td>14.5</td>
                        <td class="text-muted">(kosongkan)</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <ul class="text-muted small">
            <li>Kolom bertanda <strong>*</strong> wajib diisi. Baris pertama (header) otomatis dilewati.</li>
            <li>Kolom H (N/T/O/B) tidak perlu diisi; isinya akan diabaikan karena status dihitung otomatis dari riwayat penimbangan.</li>
            <li>Format tanggal: <code>YYYY-MM-DD</code> (mis. 2026-05-09) atau format tanggal Excel.</li>
            <li>Jika NIK tidak ditemukan di data anak, baris itu dilewati dan dilaporkan.</li>
            <li>Pengukuran dengan NIK & tanggal ukur yang sudah ada akan dilewati agar tidak dobel.</li>
        </ul>

        <form action="/anak/import-pengukuran" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Pilih File Excel</label>
                <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <a href="/anak/hasil-pengukuran" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Import Pengukuran</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
