<?= $this->extend('orangtua/layout') ?>

<?= $this->section('head') ?>
<style>
    .artikel-konten{line-height:1.8;font-size:.95rem;color:#333;}
    .artikel-konten h1,.artikel-konten h2,.artikel-konten h3{color:var(--pos-primary-dark);margin-top:24px;margin-bottom:12px;}
    .artikel-konten p{margin-bottom:16px;}
    .artikel-konten ul,.artikel-konten ol{padding-left:24px;margin-bottom:16px;}
    .artikel-konten li{margin-bottom:8px;}
    .artikel-konten img{max-width:100%;height:auto;border-radius:10px;}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">

        <a href="/orangtua/dashboard#artikel" class="btn btn-sm btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

        <div class="pos-card">
            <div class="card-body p-4 p-md-5">
                <?php
                $warnaKategori = [
                    'Gizi'           => '#1f7a5f',
                    'Tumbuh Kembang' => '#2563a8',
                    'Imunisasi'      => '#b9740f',
                    'Kesehatan'      => '#2e7da0',
                    'Lainnya'        => '#6b7a90',
                ];
                $wKat = $warnaKategori[$artikel['kategori']] ?? '#6b7a90';
                ?>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="pos-badge text-white" style="background-color:<?= esc($wKat, 'attr') ?>"><?= esc($artikel['kategori']) ?></span>
                    <span class="text-secondary small"><i class="bi bi-calendar3"></i> <?= esc(tglIndo($artikel['created_at'])) ?></span>
                </div>

                <h4 class="fw-bold mb-4" style="color:var(--pos-primary-dark);line-height:1.4"><?= esc($artikel['judul']) ?></h4>

                <hr style="border-color:var(--pos-primary-soft);border-width:2px;">

                <div class="artikel-konten mt-4"><?= nl2br($artikel['konten'] ?? '') ?></div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/orangtua/dashboard#artikel" class="btn text-white" style="background:var(--pos-primary)"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
