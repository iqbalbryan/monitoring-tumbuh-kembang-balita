<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
    .adm-stat{border-radius:16px;padding:18px 20px;color:#fff;display:flex;align-items:center;gap:14px;box-shadow:0 4px 14px rgba(40,60,100,0.08);height:100%;}
    .adm-stat .ico{width:48px;height:48px;border-radius:13px;background:rgba(255,255,255,0.22);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;}
    .adm-stat .v{font-size:1.75rem;font-weight:700;line-height:1;}
    .adm-stat .l{font-size:.8rem;opacity:1;color:#6B7890;margin-top:3px;}
    .adm-card{border:none;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);}
    .adm-card .card-header{background:transparent;border-bottom:1px solid #eef1f5;font-weight:700;color:#1a3a6b;padding:16px 20px;}
    .adm-card .card-header h6{color:#1a3a6b;}
    .adm-thead th{background:#1a3a6b;color:#fff;font-weight:600;font-size:.82rem;border-color:#1a3a6b;}
    .grad-blue{background:#EDF3FC;color:#2F5E96;}
    .grad-blue .ico{background:#D8E6F9;color:#3168A8;}
    .grad-green{background:#EAF6F0;color:#2C7D5E;}
    .grad-green .ico{background:#D3ECDF;color:#2C8763;}
    .grad-amber{background:#FBF3E1;color:#A9772A;}
    .grad-amber .ico{background:#F4E5C2;color:#B07D22;}
    .grad-red{background:#FBECE8;color:#BB5747;}
    .grad-red .ico{background:#F6D9D2;color:#C55A48;}
    .grad-neutral{background:#F3F6FA;color:#51617A;}
    .grad-neutral .ico{background:#E4EBF3;color:#51617A;}
    .badge-soft-red{background:#FBECE8;color:#BB5747;font-weight:600;}
    .badge-soft-amber{background:#FBF3E1;color:#A9772A;font-weight:600;}
    .badge-soft-green{background:#EAF6F0;color:#2C7D5E;font-weight:600;}
    .badge-soft-info{background:#E9F4F7;color:#2C7186;font-weight:600;}
    .badge-soft-neutral{background:#F0F2F6;color:#6B7890;font-weight:600;}
    .badge-alert{background:#E27B66;color:#fff;font-weight:600;}
    .adm-section-title{font-size:1.05rem;font-weight:700;color:#1a3a6b;margin:28px 0 14px;padding-bottom:8px;border-bottom:2px solid #e7f0fb;display:flex;align-items:center;gap:9px;}
    .adm-section-title:first-of-type{margin-top:6px;}
    .adm-section-title i{color:#2563a8;}
        .chart-wrapper-admin {
        position: relative;
        width: 100%;
        height: 260px;
        min-height: 260px;
    }

    .chart-wrapper-admin canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }
</style>

<?php
$namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$labelPeriode = ($namaBulan[$bulan] ?? '') . ' ' . $tahun;
?>
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h4 class="mb-0">Dashboard Admin</h4>
        <p class="mb-0">Selamat datang, <?= esc(session()->get('user_nama')) ?>! &mdash; <?= esc(tglIndo()) ?></p>
    </div>
    <form method="get" action="/admin/dashboard" class="d-flex align-items-end gap-2">
        <div>
            <label class="form-label small text-muted mb-1"><i class="bi bi-funnel"></i> Periode Data</label>
            <div class="d-flex gap-2">
                <select name="bulan" class="form-select form-select-sm" style="min-width:130px" onchange="this.form.submit()">
                    <?php foreach ($namaBulan as $num => $nm): ?>
                    <option value="<?= esc($num, 'attr') ?>" <?= $bulan == $num ? 'selected' : '' ?>>
                        <?= esc($nm) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="form-select form-select-sm" style="min-width:95px" onchange="this.form.submit()">
                    <?php foreach ($tahunList as $th): ?>
                    <option value="<?= esc($th, 'attr') ?>" <?= $tahun == $th ? 'selected' : '' ?>>
                        <?= esc($th) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($kandidat_lulus) && $kandidat_lulus > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 border-0" style="border-radius:14px;box-shadow:0 2px 10px rgba(230,169,58,0.15);">
    <i class="bi bi-mortarboard-fill fs-4"></i>
    <div class="flex-grow-1">
        <strong><?= esc($kandidat_lulus) ?> anak</strong> sudah berusia &ge; 5 tahun (60 bulan) namun masih berstatus <strong>Aktif</strong>. Tinjau untuk ditandai sebagai <strong>Lulus</strong>.
    </div>
    <a href="/anak?status=kandidat" class="btn btn-sm btn-warning fw-semibold text-nowrap">Tinjau Data</a>
</div>
<?php endif; ?>

<h5 class="adm-section-title"><i class="bi bi-clipboard2-data"></i> Ringkasan Anak</h5>

<!-- STAT CARDS ROW 1 -->
<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-blue">
            <div class="ico"><i class="bi bi-people-fill"></i></div>
            <div><div class="v"><?= esc($total_anak) ?></div><div class="l">Total Anak Aktif</div></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-green">
            <div class="ico"><i class="bi bi-rulers"></i></div>
            <div><div class="v"><?= esc($total_pengukuran_bulan) ?></div><div class="l">Ditimbang <?= esc($namaBulan[$bulan] ?? '') ?></div></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-amber">
            <div class="ico"><i class="bi bi-clipboard2-x-fill"></i></div>
            <div><div class="v"><?= esc($belum_diukur) ?></div><div class="l">Belum Diukur <?= esc($namaBulan[$bulan] ?? '') ?></div></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-red">
            <div class="ico"><i class="bi bi-mortarboard-fill"></i></div>
            <div><div class="v"><?= esc($kandidat_lulus) ?></div><div class="l">Kandidat Lulus (&ge;60 bln)</div></div>
        </div>
    </div>
</div>

<!-- GRAFIK PENGUKURAN -->
<div class="adm-card mb-4">
    <div class="card-header"><h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill"></i> Grafik Pengukuran Per Bulan &mdash; Tahun <?= esc($tahun) ?></h6></div>
    <div class="card-body">
    <div class="chart-wrapper-admin">
        <canvas id="grafikPengukuran"></canvas>
    </div>
</div>
</div>

<h5 class="adm-section-title"><i class="bi bi-heart-pulse"></i> Status Kesehatan</h5>

<?php if (($total_pengukuran_bulan ?? 0) == 0): ?>
<div class="adm-card mb-4">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-clipboard-x" style="font-size:2.5rem;color:#9bb3d4"></i>
        <p class="mt-3 mb-1">Belum ada pengukuran pada periode <strong><?= esc($namaBulan[$bulan] ?? '') ?> <?= esc($tahun) ?></strong>.</p>
        <p class="small mb-0">Pilih periode lain di atas, atau input pengukuran baru lewat menu Input Pengukuran.</p>
    </div>
</div>
<?php endif; ?>

<!-- STATUS GIZI BB/U -->
<div class="adm-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard2-data"></i> Status Gizi (BB/U) - Berat Badan menurut Umur</h6>
        <?php $bbuMasalah = ($giziStats['BB Sangat Kurang'] ?? 0) + ($giziStats['BB Kurang'] ?? 0); ?>
        <?php if ($bbuMasalah > 0): ?>
            <span class="badge badge-alert"><?= esc($bbuMasalah) ?> anak BB kurang/sangat kurang</span>
        <?php else: ?>
            <span class="badge badge-soft-green">Tidak ada masalah gizi</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-green">
                    <div class="ico"><i class="bi bi-check-circle-fill"></i></div>
                    <div><div class="v"><?= esc($giziStats['BB Normal'] ?? 0) ?></div><div class="l">BB Normal</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-amber">
                    <div class="ico"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div><div class="v"><?= esc($giziStats['BB Kurang'] ?? 0) ?></div><div class="l">BB Kurang</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-red">
                    <div class="ico"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    <div><div class="v"><?= esc($giziStats['BB Sangat Kurang'] ?? 0) ?></div><div class="l">BB Sangat Kurang</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-neutral">
                    <div class="ico"><i class="bi bi-arrow-up-circle-fill"></i></div>
                    <div><div class="v"><?= esc($giziStats['Risiko BB Lebih'] ?? 0) ?></div><div class="l">Risiko BB Lebih</div></div>
                </div>
            </div>
        </div>
        <p class="text-secondary small mb-0 mt-3"><i class="bi bi-info-circle"></i> Klasifikasi berdasarkan indeks Berat Badan menurut Umur (BB/U) standar WHO pada periode <?= esc($labelPeriode) ?>.</p>
    </div>
</div>

<!-- STATUS PERTUMBUHAN TB/U (STUNTING) -->
<div class="adm-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-rulers"></i> Status Pertumbuhan (PB/U atau TB/U) - Deteksi Stunting</h6>
       <?php if ($total_stunting > 0): ?>
            <span class="badge badge-alert"><?= esc($total_stunting) ?> anak terindikasi stunting</span>
        <?php else: ?>
            <span class="badge badge-soft-green">Tidak ada indikasi stunting</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-green">
                    <div class="ico"><i class="bi bi-check-circle-fill"></i></div>
                    <div><div class="v"><?= esc($tbuStats['Normal'] ?? 0) ?></div><div class="l">Normal</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-amber">
                    <div class="ico"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div><div class="v"><?= esc($tbuStats['Pendek'] ?? 0) ?></div><div class="l">Pendek (Stunting)</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-red">
                    <div class="ico"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    <div><div class="v"><?= esc($tbuStats['Sangat Pendek'] ?? 0) ?></div><div class="l">Sangat Pendek</div></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="adm-stat grad-neutral">
                    <div class="ico"><i class="bi bi-arrow-up-circle-fill"></i></div>
                    <div><div class="v"><?= esc($tbuStats['Tinggi'] ?? 0) ?></div><div class="l">Tinggi</div></div>
                </div>
            </div>
        </div>
        <p class="text-secondary small mb-0 mt-3"><i class="bi bi-info-circle"></i> Klasifikasi berdasarkan indeks Panjang/Tinggi Badan menurut Umur (PB/U atau TB/U) standar WHO pada periode <?= esc($labelPeriode) ?>. Z-score di bawah -2 SD dikategorikan <strong>stunting</strong>.</p>
    </div>
</div>

<!-- STATUS GIZI BB/TB (WASTING) -->
<div class="adm-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard2-pulse"></i> Status Gizi (BB/TB) - Deteksi Wasting</h6>
        <?php if ($total_wasting > 0): ?>
            <span class="badge badge-alert"><?= esc($total_wasting) ?> anak gizi kurang/buruk</span>
        <?php else: ?>
            <span class="badge badge-soft-green">Tidak ada indikasi wasting</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-red">
                    <div class="ico"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Gizi Buruk'] ?? 0) ?></div><div class="l">Gizi Buruk</div></div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-amber">
                    <div class="ico"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Gizi Kurang'] ?? 0) ?></div><div class="l">Gizi Kurang</div></div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-green">
                    <div class="ico"><i class="bi bi-check-circle-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Gizi Baik'] ?? 0) ?></div><div class="l">Gizi Baik</div></div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-neutral">
                    <div class="ico"><i class="bi bi-info-circle-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Berisiko Gizi Lebih'] ?? 0) ?></div><div class="l">Berisiko Lebih</div></div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-amber">
                    <div class="ico"><i class="bi bi-arrow-up-circle-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Gizi Lebih'] ?? 0) ?></div><div class="l">Gizi Lebih</div></div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="adm-stat grad-red">
                    <div class="ico"><i class="bi bi-emoji-frown-fill"></i></div>
                    <div><div class="v"><?= esc($bbtbStats['Obesitas'] ?? 0) ?></div><div class="l">Obesitas</div></div>
                </div>
            </div>
        </div>
        <p class="text-secondary small mb-0 mt-3"><i class="bi bi-info-circle"></i> Klasifikasi berdasarkan indeks Berat Badan menurut Tinggi Badan (BB/TB) standar WHO pada periode <?= esc($labelPeriode) ?>. Z-score di bawah -2 SD menandakan <strong>wasting (kurus)</strong>.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="adm-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle-fill"></i> Anak yang Perlu Perhatian</h6>
                <span class="badge badge-alert"><?= esc(count($anakPerhatian)) ?> anak</span>
            </div>
            <div class="card-body">
                <?php if (!empty($anakPerhatian)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="adm-thead">
                            <tr>
                                <th>Nama</th>
                                <th>JK</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>Status Gizi (BB/U)</th>
                                <th>Stunting (PB/U atau TB/U)</th>
                                <th>Wasting (BB/TB)</th>
                                <th>Tgl Ukur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anakPerhatian as $a): ?>
                            <tr>
                                <td class="fw-semibold"><a href="/anak/detail/<?= esc($a['id'], 'url') ?>" class="text-decoration-none" title="Lihat detail anak"><?= esc($a['nama']) ?></a></td>
                                <td><?= esc($a['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L') ?></td>
                                <td><?= esc($a['berat_badan']) ?></td>
                                <td><?= esc($a['tinggi_badan']) ?></td>
                                <td><span class="badge <?= esc($a['status_gizi'] === 'BB Sangat Kurang' ? 'badge-soft-red' : 'badge-soft-amber', 'attr') ?>">
                                    <?= esc($a['status_gizi']) ?>
                                </span></td>
                                <td>
                                    <?php
                                        $ztb = $a['z_score_tb_u'];
                                        if ($ztb === null || $ztb === '') { $stunt = '-'; $stuntCls = 'badge-soft-neutral'; }
                                        elseif ($ztb < -3) { $stunt = 'Sangat Pendek'; $stuntCls = 'badge-soft-red'; }
                                        elseif ($ztb < -2) { $stunt = 'Pendek'; $stuntCls = 'badge-soft-amber'; }
                                        elseif ($ztb <= 2) { $stunt = 'Normal'; $stuntCls = 'badge-soft-green'; }
                                        else { $stunt = 'Tinggi'; $stuntCls = 'badge-soft-info'; }
                                    ?>
                                    <span class="badge <?= esc($stuntCls, 'attr') ?>"><?= esc($stunt) ?></span>
                                </td>
                                <td>
                                    <?php
                                        $zbt = $a['z_score_bb_tb'];
                                        if ($zbt === null || $zbt === '') { $wast = '-'; $wastCls = 'badge-soft-neutral'; }
                                        elseif ($zbt < -3) { $wast = 'Gizi Buruk'; $wastCls = 'badge-soft-red'; }
                                        elseif ($zbt < -2) { $wast = 'Gizi Kurang'; $wastCls = 'badge-soft-amber'; }
                                        elseif ($zbt <= 2) { $wast = 'Gizi Baik'; $wastCls = 'badge-soft-green'; }
                                        else { $wast = 'Gizi Lebih'; $wastCls = 'badge-soft-info'; }
                                    ?>
                                    <span class="badge <?= esc($wastCls, 'attr') ?>"><?= esc($wast) ?></span>
                                </td>
                                <td><?= esc(date('d-m-Y', strtotime($a['tanggal_ukur']))) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-emoji-smile" style="font-size:2.5rem;color:#28A745"></i>
                    <p class="mt-2 small">Semua anak dalam kondisi gizi baik!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<h5 class="adm-section-title"><i class="bi bi-person-gear"></i> Manajemen Akun</h5>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<div class="d-flex justify-content-end mb-3">
        <form action="/admin/kirim-notifikasi" method="post" class="d-inline" onsubmit="return confirm('Kirim notifikasi terjadwal sekarang? (jadwal posyandu, pengingat penimbangan, anak akan lulus)')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bell"></i> Kirim Notifikasi Sekarang
        </button>
    </form>
</div>

<!-- STAT CARDS ROW 2 -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-blue">
            <div class="ico"><i class="bi bi-person-vcard-fill"></i></div>
            <div><div class="v"><?= esc($total_user) ?></div><div class="l">Total Orang Tua</div></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="adm-stat grad-green">
            <div class="ico"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="v"><?= esc($total_aktif) ?></div><div class="l">Akun Aktif</div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="adm-card h-100">
            <div class="card-header"><h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event"></i> Jadwal Posyandu Berikutnya</h6></div>
            <div class="card-body">
                <?php if ($jadwalBerikutnya): ?>
                <div style="border-left:4px solid #5bc8f5;padding:12px 16px;border-radius:11px;background:linear-gradient(135deg,#fff,#e7f0fb);">
                    <div class="fw-bold" style="color:#1a3a6b"><?= esc($jadwalBerikutnya['judul']) ?></div>
                    <div class="text-muted small mt-1"><i class="bi bi-calendar3"></i> <?= esc(tglIndo($jadwalBerikutnya['tanggal'])) ?></div>
                    <div class="text-muted small"><i class="bi bi-clock"></i> <?= esc(date('H:i', strtotime($jadwalBerikutnya['waktu_mulai']))) ?> - <?= esc(date('H:i', strtotime($jadwalBerikutnya['waktu_selesai']))) ?> WIB</div>
                    <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= esc($jadwalBerikutnya['lokasi']) ?></div>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size:2.5rem;"></i>
                    <p class="mt-2 small">Belum ada jadwal</p>
                    <a href="/admin/jadwal/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Grafik Pengukuran Per Bulan
const grafikData = <?= $grafikPengukuran ?>;
const _curIdx = grafikData.length - 1;
const _barColors = grafikData.map((d, i) => i === _curIdx ? 'rgba(37,99,168,0.92)' : 'rgba(95,181,140,0.60)');
const _barBorder = grafikData.map((d, i) => i === _curIdx ? '#1a3a6b' : '#5FB58C');
const datalabelPlugin = {
    id: 'customDatalabels',
    afterDatasetsDraw(chart) {
        const {ctx} = chart;
        chart.data.datasets.forEach((dataset, i) => {
            chart.getDatasetMeta(i).data.forEach((dp, idx) => {
                const val = dataset.data[idx];
                if (!val) return;
                const {x, y} = dp.getProps(['x','y'], true);
                ctx.save();
                ctx.fillStyle = idx === _curIdx ? '#1a3a6b' : '#4a7a62';
                ctx.font = 'bold 11px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillText(val, x, y - 3);
                ctx.restore();
            });
        });
    }
};
window.chartPengukuran = new Chart(document.getElementById('grafikPengukuran'), {
    type: 'bar',
    data: {
        labels: grafikData.map(d => d.bulan),
        datasets: [{
            label: 'Jumlah Pengukuran',
            data: grafikData.map(d => d.total),
            backgroundColor: _barColors,
            borderColor: _barBorder,
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 0
        },
        animations: false,
        resizeDelay: 0,
        layout: {
            padding: {
                top: 24
            }
        },
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grace: '12%',
                ticks: {
                    stepSize: 1
                },
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    },
    plugins: [datalabelPlugin]
});
window.addEventListener('load', function () {
    setTimeout(function () {
        if (window.chartPengukuran) {
            window.chartPengukuran.resize();
            window.chartPengukuran.update('none');
        }
    }, 800);
});
</script>

<?= $this->endSection() ?>
