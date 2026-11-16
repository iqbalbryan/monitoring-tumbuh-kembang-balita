<?= $this->extend('orangtua/layout') ?>

<?= $this->section('content') ?>
<div class="pos-card">
    <div class="card-body">
        <div class="pos-empty">
            <div class="ico"><i class="bi bi-people"></i></div>
            <h5 class="fw-bold mt-3" style="color:var(--pos-primary-dark)">Belum ada anak terdaftar</h5>
            <p class="small mb-3">Hubungkan data anak Anda dengan memasukkan NIK yang sudah terdaftar di Posyandu Nusa Indah untuk mulai memantau tumbuh kembangnya.</p>
            <a href="/orangtua/tambah-anak" class="btn btn-sm text-white" style="background:var(--pos-primary)"><i class="bi bi-plus-lg"></i> Tambah Anak Sekarang</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
