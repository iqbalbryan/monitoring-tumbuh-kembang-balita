<?= $this->extend('orangtua/layout') ?>

<?= $this->section('head') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
    .stat-mini{border-radius:13px;padding:16px;text-align:center;}
    .stat-mini .v{font-size:1.5rem;font-weight:700;line-height:1.1;}
    .stat-mini .l{font-size:.76rem;color:var(--pos-muted);margin-top:2px;}
    .jadwal-card{border-left:4px solid var(--pos-primary);border-radius:11px;background:linear-gradient(135deg,#fff,var(--pos-primary-soft));padding:12px 16px;margin-bottom:10px;}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <?php $cWarna = $anak['jenis_kelamin'] === 'Perempuan' ? '#e84f8b' : '#3a8dde'; $cIcon = $anak['jenis_kelamin'] === 'Perempuan' ? 'bi-gender-female' : 'bi-gender-male'; ?>
        <div style="width:52px;height:52px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;background:<?= $cWarna ?>1a;color:<?= $cWarna ?>"><i class="bi <?= $cIcon ?>"></i></div>
        <div>
            <h5 class="fw-bold mb-0" style="color:var(--pos-primary-dark)"><?= esc($anak['nama']) ?></h5>
            <span class="text-secondary small"><?= $umur->y > 0 ? $umur->y . ' tahun ' . $umur->m . ' bulan' : $umur->m . ' bulan' ?> &middot; <?= esc($anak['jenis_kelamin']) ?> &middot; Lahir <?= date('d F Y', strtotime($anak['tanggal_lahir'])) ?></span>
        </div>
    </div>
    <a href="/orangtua/dashboard" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php
$statusGizi = $pengukuranTerakhir['status_gizi'] ?? null;
$warnaAlert = [
    'BB Sangat Kurang' => ['bg' => '#fde4e1', 'text' => '#c0392b', 'icon' => 'bi-exclamation-octagon-fill'],
    'BB Kurang'        => ['bg' => '#fdf0dd', 'text' => '#b9740f', 'icon' => 'bi-exclamation-triangle-fill'],
    'Risiko BB Lebih'  => ['bg' => '#e7f4fb', 'text' => '#2e7da0', 'icon' => 'bi-info-circle-fill'],
    'BB Normal'        => ['bg' => '#e3f5ee', 'text' => '#1f7a5f', 'icon' => 'bi-check-circle-fill'],
];
$alert = $warnaAlert[$statusGizi] ?? null;

// Status pertumbuhan TB/U (deteksi stunting) dari z-score tinggi-per-umur
helper('zscore');
$zTBU = $pengukuranTerakhir['z_score_tb_u'] ?? null;
$statusTB = ($zTBU !== null && $zTBU !== '') ? statusPertumbuhanTBU((float) $zTBU) : null;
$warnaStunting = [
    'Sangat Pendek' => ['bg' => '#fde4e1', 'text' => '#c0392b', 'icon' => 'bi-exclamation-octagon-fill'],
    'Pendek'        => ['bg' => '#fdf0dd', 'text' => '#b9740f', 'icon' => 'bi-exclamation-triangle-fill'],
    'Normal'        => ['bg' => '#e3f5ee', 'text' => '#1f7a5f', 'icon' => 'bi-check-circle-fill'],
    'Tinggi'        => ['bg' => '#e7f4fb', 'text' => '#2e7da0', 'icon' => 'bi-info-circle-fill'],
];
$alertTB = $statusTB ? ($warnaStunting[$statusTB] ?? null) : null;

// Interpretasi singkat z-score agar mudah dipahami orang tua
$interpretasiBBU = [
    'BB Sangat Kurang' => 'Berat badan sangat kurang',
    'BB Kurang'        => 'Berat badan kurang',
    'Risiko BB Lebih'  => 'Berat badan berlebih',
    'BB Normal'        => 'Berat badan ideal sesuai umur',
];
$interpretasiTBU = [
    'Sangat Pendek' => 'Jauh lebih pendek dari standar',
    'Pendek'        => 'Lebih pendek dari standar',
    'Normal'        => 'Tinggi badan sesuai umur',
    'Tinggi'        => 'Lebih tinggi dari standar',
];
$ketBBU = $interpretasiBBU[$statusGizi] ?? null;
$ketTBU = $statusTB ? ($interpretasiTBU[$statusTB] ?? null) : null;

// Status Gizi BB/TB (deteksi wasting) dari standar WHO berat-per-tinggi
$dbBBTB = \Config\Database::connect();
$zBBTB = $pengukuranTerakhir['z_score_bb_tb'] ?? null;
if (($zBBTB === null || $zBBTB === '') && !empty($pengukuranTerakhir['berat_badan']) && !empty($pengukuranTerakhir['tinggi_badan'])) {
    $zBBTB = hitungZScoreBBTB((float) $pengukuranTerakhir['berat_badan'], (float) $pengukuranTerakhir['tinggi_badan'], $anak['jenis_kelamin'], $dbBBTB);
}
$statusBBTB = ($zBBTB !== null && $zBBTB !== '') ? statusGiziBBTB((float) $zBBTB) : null;
$warnaGiziBBTB = [
    'Gizi Buruk'          => ['bg' => '#fde4e1', 'text' => '#c0392b', 'icon' => 'bi-exclamation-octagon-fill'],
    'Gizi Kurang'         => ['bg' => '#fdf0dd', 'text' => '#b9740f', 'icon' => 'bi-exclamation-triangle-fill'],
    'Gizi Baik'           => ['bg' => '#e3f5ee', 'text' => '#1f7a5f', 'icon' => 'bi-check-circle-fill'],
    'Berisiko Gizi Lebih' => ['bg' => '#fdf0dd', 'text' => '#b9740f', 'icon' => 'bi-info-circle-fill'],
    'Gizi Lebih'          => ['bg' => '#e7f4fb', 'text' => '#2e7da0', 'icon' => 'bi-info-circle-fill'],
    'Obesitas'            => ['bg' => '#e7f4fb', 'text' => '#2e7da0', 'icon' => 'bi-exclamation-octagon-fill'],
];
$alertBBTB = $statusBBTB ? ($warnaGiziBBTB[$statusBBTB] ?? null) : null;
$interpretasiBBTB = [
    'Gizi Buruk'          => 'Sangat kurus (wasting berat)',
    'Gizi Kurang'         => 'Kurus (wasting)',
    'Gizi Baik'           => 'Proporsi berat & tinggi ideal',
    'Berisiko Gizi Lebih' => 'Berisiko gemuk',
    'Gizi Lebih'          => 'Gemuk',
    'Obesitas'            => 'Sangat gemuk (obesitas)',
];
$ketBBTB = $statusBBTB ? ($interpretasiBBTB[$statusBBTB] ?? null) : null;
?>

<?php if ($alertTB && in_array($statusTB, ['Pendek', 'Sangat Pendek'])): ?>
<div class="d-flex align-items-center gap-3 mb-3" style="background:<?= $alertTB['bg'] ?>;border-radius:14px;padding:14px 18px;color:<?= $alertTB['text'] ?>">
    <i class="bi <?= $alertTB['icon'] ?> fs-4"></i>
    <div>
        <div class="fw-bold">Indikasi Stunting: <?= esc($statusTB) ?> (TB/U)</div>
        <div class="small">
            <?php if ($statusTB === 'Sangat Pendek'): ?>
            Tinggi badan anak sangat kurang untuk usianya. Segera konsultasikan ke tenaga kesehatan atau dokter anak untuk penanganan lebih lanjut.
            <?php else: ?>
            Tinggi badan anak kurang untuk usianya. Tingkatkan asupan gizi seimbang dan pantau pertumbuhan secara rutin tiap bulan.
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($alert): ?>
<div class="d-flex align-items-center gap-3 mb-4" style="background:<?= $alert['bg'] ?>;border-radius:14px;padding:14px 18px;color:<?= $alert['text'] ?>">
    <i class="bi <?= $alert['icon'] ?> fs-4"></i>
    <div>
        <div class="fw-bold">Status Gizi: <?= esc($statusGizi) ?></div>
        <div class="small">
            <?php if ($statusGizi === 'BB Sangat Kurang'): ?>
                Segera konsultasikan dengan petugas kesehatan atau dokter anak!
            <?php elseif ($statusGizi === 'BB Kurang'): ?>
                Perhatikan asupan nutrisi anak dan konsultasikan dengan petugas posyandu.
            <?php elseif ($statusGizi === 'Risiko BB Lebih'): ?>
                Perhatikan pola makan anak dan konsultasikan dengan petugas posyandu.
            <?php else: ?>
                Status gizi anak baik! Pertahankan pola makan dan nutrisi yang baik.
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($alertBBTB && in_array($statusBBTB, ['Gizi Buruk', 'Gizi Kurang'])): ?>
<div class="d-flex align-items-center gap-3 mb-4" style="background:<?= $alertBBTB['bg'] ?>;border-radius:14px;padding:14px 18px;color:<?= $alertBBTB['text'] ?>">
    <i class="bi <?= $alertBBTB['icon'] ?> fs-4"></i>
    <div>
        <div class="fw-bold">Status Gizi (BB/TB): <?= esc($statusBBTB) ?></div>
        <div class="small">
            <?php if ($statusBBTB === 'Gizi Buruk'): ?>
                Anak sangat kurus (wasting berat). Segera konsultasikan ke petugas kesehatan atau dokter anak.
            <?php else: ?>
                Berat badan anak kurang untuk tinggi badannya (kurus). Tingkatkan asupan gizi seimbang dan pantau rutin.
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-12">

        <!-- PROFIL ANAK -->
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-3"><i class="bi bi-person-vcard"></i> Profil Anak</h6>
                <div class="row g-3">
                    <div class="col-md-4 col-6"><div class="text-secondary small">NIK Anak</div><div class="fw-semibold"><?= esc($anak['nik_anak'] ?? '-') ?></div></div>
                    <div class="col-md-4 col-6"><div class="text-secondary small">Tanggal Lahir</div><div class="fw-semibold"><?= date('d F Y', strtotime($anak['tanggal_lahir'])) ?></div></div>
                    <div class="col-md-4 col-6"><div class="text-secondary small">Berat Lahir</div><div class="fw-semibold"><?= (isset($anak['berat_badan_lahir']) && $anak['berat_badan_lahir'] > 0) ? esc($anak['berat_badan_lahir']) . ' kg' : '-' ?></div></div>
                    <div class="col-md-4 col-6"><div class="text-secondary small">Panjang Lahir</div><div class="fw-semibold"><?= (isset($anak['panjang_badan_lahir']) && $anak['panjang_badan_lahir'] > 0) ? esc($anak['panjang_badan_lahir']) . ' cm' : '-' ?></div></div>
                    <div class="col-md-4 col-6"><div class="text-secondary small">Nama Ibu</div><div class="fw-semibold"><?= esc($anak['nama_ibu'] ?? '-') ?></div></div>
                    <div class="col-md-4 col-6"><div class="text-secondary small">Status Lahir</div><div class="fw-semibold"><?= esc($anak['premature'] ?? '-') ?></div></div>
                </div>
            </div>
        </div>

        <!-- HASIL POSYANDU TERAKHIR -->
        <?php if ($pengukuranTerakhir): ?>
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-1"><i class="bi bi-clipboard2-pulse"></i> Hasil Posyandu Terakhir</h6>
                <div class="text-secondary small mb-3">Tanggal: <?= date('d F Y', strtotime($pengukuranTerakhir['tanggal_ukur'])) ?></div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-mini" style="background:#e7f0fb"><div class="v" style="color:#2563a8"><?= esc($pengukuranTerakhir['berat_badan']) ?></div><div class="l">Berat Badan (kg)</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-mini" style="background:#e3f5ee"><div class="v" style="color:#1f7a5f"><?= esc($pengukuranTerakhir['tinggi_badan']) ?></div><div class="l">Tinggi Badan (cm)</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-mini" style="background:#eef2f7"><div class="v" style="color:#2e5aa0"><?= (!empty($pengukuranTerakhir['lila']) && (float) $pengukuranTerakhir['lila'] > 0) ? esc($pengukuranTerakhir['lila']) : '-' ?></div><div class="l">LiLA (cm)</div></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <?php $ntob = $pengukuranTerakhir['ntob'] ?? '-'; $ntobLabel = ['N' => 'Naik', 'T' => 'Tidak Naik', 'O' => 'Tdk Ditimbang Bln Lalu', 'B' => 'Baru']; ?>
                        <div class="stat-mini" style="background:#eef2f7"><div class="v" style="color:#2e5aa0"><?= esc($ntob) ?></div><div class="l"><?= $ntobLabel[$ntob] ?? '-' ?></div></div>
                    </div>
                    <?php if ($pengukuranTerakhir['z_score_bb_u']): ?>
                    <div class="col-md-4">
                        <div class="stat-mini" style="background:#f5f9ff;text-align:left">
                            <div class="d-flex justify-content-between align-items-center"><span class="l">Z-Score BB/U</span><?php if ($statusGizi): ?><span class="badge" style="background:<?= $alert['bg'] ?? '#e7f0fb' ?>;color:<?= $alert['text'] ?? '#2563a8' ?>;font-weight:600"><?= esc($statusGizi) ?></span><?php endif; ?></div>
                            <div class="fw-bold" style="font-size:1.15rem"><?= esc($pengukuranTerakhir['z_score_bb_u']) ?> <span class="text-secondary" style="font-size:.8rem;font-weight:500">SD</span></div>
                            <?php if ($ketBBU): ?><div class="text-secondary small"><?= esc($ketBBU) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini" style="background:#f5f9ff;text-align:left">
                            <div class="d-flex justify-content-between align-items-center"><span class="l">Z-Score TB/U</span><?php if ($statusTB): ?><span class="badge" style="background:<?= $alertTB['bg'] ?>;color:<?= $alertTB['text'] ?>;font-weight:600"><?= $statusTB ?></span><?php endif; ?></div>
                            <div class="fw-bold" style="font-size:1.15rem"><?= $pengukuranTerakhir['z_score_tb_u'] ?? '-' ?> <span class="text-secondary" style="font-size:.8rem;font-weight:500">SD</span></div>
                            <?php if ($ketTBU): ?><div class="text-secondary small"><?= esc($ketTBU) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-mini" style="background:#f5f9ff;text-align:left">
                            <div class="d-flex justify-content-between align-items-center"><span class="l">Status Gizi (BB/TB)</span><?php if ($statusBBTB): ?><span class="badge" style="background:<?= $alertBBTB['bg'] ?>;color:<?= $alertBBTB['text'] ?>;font-weight:600"><?= esc($statusBBTB) ?></span><?php endif; ?></div>
                            <div class="fw-bold" style="font-size:1.15rem"><?= ($zBBTB !== null && $zBBTB !== '') ? esc($zBBTB) : '-' ?> <span class="text-secondary" style="font-size:.8rem;font-weight:500">SD</span></div>
                            <?php if ($ketBBTB): ?><div class="text-secondary small"><?= esc($ketBBTB) ?></div><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- GRAFIK TUMBUH KEMBANG -->
        <?php if (!empty($pengukuran)): ?>
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-3"><i class="bi bi-graph-up-arrow"></i> Grafik Tumbuh Kembang</h6>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-secondary small fw-semibold">Berat Badan (kg)</h6>
                        <canvas id="grafikBB"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-secondary small fw-semibold">Tinggi Badan (cm)</h6>
                        <canvas id="grafikTB"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- KMS DIGITAL -->
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-1"><i class="bi bi-card-checklist"></i> Grafik Berat Badan / Umur (Deteksi Underweight)</h6>
                <p class="text-secondary small">Grafik berat badan anak dibandingkan dengan standar WHO (0-60 bulan)</p>
                <?php if ($umurBulan > 60): ?>
                <div class="alert alert-info small py-2"><i class="bi bi-info-circle"></i> Anak sudah berusia lebih dari 60 bulan. KMS ditampilkan berdasarkan data pengukuran dalam rentang 0-60 bulan.</div>
                <?php endif; ?>
                <canvas id="grafikKMS" height="120"></canvas>
            </div>
        </div>

        <!-- KMS TB/U DIGITAL (Stunting) -->
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-1"><i class="bi bi-rulers"></i> Grafik Tinggi Badan / Umur (Deteksi Stunting)</h6>
                <p class="text-secondary small">Tinggi badan anak dibandingkan standar WHO. Di bawah garis -2 SD menandakan <strong>stunting (pendek)</strong>.</p>
                <canvas id="grafikKMSTB" height="120"></canvas>
            </div>
        </div>

        <!-- KMS BB/TB DIGITAL (Wasting) -->
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-1"><i class="bi bi-clipboard2-pulse"></i> Grafik Berat Badan / Tinggi Badan (Deteksi Wasting)</h6>
                <p class="text-secondary small">Berat badan anak dibandingkan standar WHO menurut tinggi badan. Di bawah garis -2 SD menandakan <strong>gizi kurang/buruk (wasting)</strong>.</p>
                <canvas id="grafikKMSBBTB" height="120"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- RIWAYAT POSYANDU -->
        <div class="pos-card mb-4">
            <div class="card-body">
                <h6 class="pos-section-title mb-3"><i class="bi bi-table"></i> Riwayat Posyandu</h6>
                <?php if (!empty($pengukuran)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:var(--pos-primary-soft)">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>LiLA</th>
                                <th>N/T/O/B</th>
                                <th>Status Gizi</th>
                                <th>Status TB/U</th>
                                <th>Status Gizi (BB/TB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach (array_reverse($pengukuran) as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($p['tanggal_ukur'])) ?></td>
                                <td><?= esc($p['berat_badan']) ?></td>
                                <td><?= esc($p['tinggi_badan']) ?></td>
                                <td><?= (!empty($p['lila']) && (float) $p['lila'] > 0) ? esc($p['lila']) : '-' ?></td>
                                <td>
                                    <?php $ntobR = $p['ntob'] ?? ''; $warnaNtob = ['N' => 'bg-success', 'T' => 'bg-danger', 'O' => 'bg-secondary', 'B' => 'bg-info']; ?>
                                    <?php if ($ntobR): ?><span class="badge <?= $warnaNtob[$ntobR] ?? 'bg-secondary' ?>"><?= $ntobR ?></span><?php else: ?>-<?php endif; ?>
                                </td>
                                <td><?= $p['status_gizi'] ?? '-' ?></td>
                                <td>
                                    <?php $zr = $p['z_score_tb_u'] ?? null; $sr = ($zr !== null && $zr !== '') ? statusPertumbuhanTBU((float) $zr) : null; ?>
                                    <?php if ($sr): ?><span class="badge" style="background:<?= $warnaStunting[$sr]['bg'] ?>;color:<?= $warnaStunting[$sr]['text'] ?>"><?= $sr ?></span><?php else: ?>-<?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $zbt = $p['z_score_bb_tb'] ?? null;
                                        if (($zbt === null || $zbt === '') && !empty($p['berat_badan']) && !empty($p['tinggi_badan'])) {
                                            $zbt = hitungZScoreBBTB((float) $p['berat_badan'], (float) $p['tinggi_badan'], $anak['jenis_kelamin'], $dbBBTB);
                                        }
                                        $sbt = ($zbt !== null && $zbt !== '') ? statusGiziBBTB((float) $zbt) : null;
                                    ?>
                                    <?php if ($sbt): ?><span class="badge" style="background:<?= $warnaGiziBBTB[$sbt]['bg'] ?>;color:<?= $warnaGiziBBTB[$sbt]['text'] ?>"><?= esc($sbt) ?></span><?php else: ?>-<?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="pos-empty py-4"><i class="bi bi-clipboard-x" style="font-size:2.2rem"></i><p class="mt-2 small mb-0">Belum ada riwayat pengukuran</p></div>
                <?php endif; ?>
            </div>
        </div>

    </div>


</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($pengukuran)): ?>
<script>
const labels   = <?= $grafikLabels ?>;
const dataBB   = <?= $grafikBB ?>;
const dataTB   = <?= $grafikTB ?>;
const kmsData  = <?= $kmsData ?>;
const kmsTBU   = <?= $kmsTBU ?>;
const kmsBBTB  = <?= $kmsBBTB ?>;

new Chart(document.getElementById('grafikBB'), {
    type: 'line',
    data: { labels: labels, datasets: [{ label: 'BB (kg)', data: dataBB, borderColor: '#2563a8', backgroundColor: 'rgba(37,99,168,0.1)', borderWidth: 2, pointBackgroundColor: '#2563a8', tension: 0.3, fill: true }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
});

new Chart(document.getElementById('grafikTB'), {
    type: 'line',
    data: { labels: labels, datasets: [{ label: 'TB (cm)', data: dataTB, borderColor: '#1f7a5f', backgroundColor: 'rgba(31,122,95,0.1)', borderWidth: 2, pointBackgroundColor: '#1f7a5f', tension: 0.3, fill: true }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
});

if (kmsData.length > 0) {
    const _maxBlnBB = Math.min(<?= (int) $umurBulan ?> + 6, 60);
    const kms = kmsData.filter(d => d.umur_bulan <= _maxBlnBB);
    const kmsLabels = kms.map(d => d.umur_bulan + ' bln');
    const kmsMedian = kms.map(d => parseFloat(d.median));
    const kmsSD2neg = kms.map(d => parseFloat(d.sd2neg));
    const kmsSD3neg = kms.map(d => parseFloat(d.sd3neg));
    const kmsSD2pos = kms.map(d => parseFloat(d.sd2pos));

    const pengukuranData = <?= json_encode(array_map(function($p) use ($anak) {
        $tglLahir  = new \DateTime($anak['tanggal_lahir']);
        $tglUkur   = new \DateTime($p['tanggal_ukur']);
        $umur      = $tglLahir->diff($tglUkur);
        $umurBulan = ($umur->y * 12) + $umur->m;
        return ['umur_bulan' => min($umurBulan, 60), 'berat_badan' => $p['berat_badan']];
    }, $pengukuran)) ?>;

    const bbAnakData = kmsLabels.map((label, i) => {
        const umurBulan = kms[i].umur_bulan;
        const found = pengukuranData.find(p => p.umur_bulan == umurBulan);
        return found ? found.berat_badan : null;
    });

    new Chart(document.getElementById('grafikKMS'), {
        type: 'line',
        data: {
            labels: kmsLabels,
            datasets: [
                { label: 'BB Anak', data: bbAnakData, borderColor: '#1a3a6b', backgroundColor: '#1a3a6b', borderWidth: 2.5, borderDash: [6, 3], pointBackgroundColor: '#1a3a6b', pointRadius: 5, pointHoverRadius: 7, tension: 0.3, fill: false, spanGaps: false, order: 1 },
                { label: '+2 SD (Risiko BB Lebih)', data: kmsSD2pos, borderColor: '#17A2B8', borderWidth: 1.5, pointRadius: 0, tension: 0.3, fill: false, order: 2 },
                { label: 'Median (Normal)', data: kmsMedian, borderColor: '#28A745', borderWidth: 2, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(40,167,69,0.1)', fill: '+1', order: 3 },
                { label: '-2 SD (BB Kurang)', data: kmsSD2neg, borderColor: '#FFC107', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(255,193,7,0.15)', fill: '+1', order: 4 },
                { label: '-3 SD (BB Sangat Kurang)', data: kmsSD3neg, borderColor: '#DC3545', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(220,53,69,0.15)', fill: 'origin', order: 5 }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true, padding: 15 } },
                tooltip: { callbacks: { label: function(context) { if (context.parsed.y !== null) { return context.dataset.label + ': ' + context.parsed.y + ' kg'; } return null; } } }
            },
            scales: {
                y: { beginAtZero: false, title: { display: true, text: 'Berat Badan (kg)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { title: { display: true, text: 'Umur (bulan)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { maxTicksLimit: 20 } }
            }
        }
    });
}

if (kmsTBU.length > 0) {
    const _maxBlnTB = Math.min(<?= (int) $umurBulan ?> + 6, 60);
    const tb = kmsTBU.filter(d => d.umur_bulan <= _maxBlnTB);
    const tbLabels = tb.map(d => d.umur_bulan + ' bln');
    const tbMedian = tb.map(d => parseFloat(d.median));
    const tbSD2neg = tb.map(d => parseFloat(d.sd2neg));
    const tbSD3neg = tb.map(d => parseFloat(d.sd3neg));
    const tbSD2pos = tb.map(d => parseFloat(d.sd2pos));

    const pengukuranTB = <?= json_encode(array_map(function($p) use ($anak) {
        $tglLahir  = new \DateTime($anak['tanggal_lahir']);
        $tglUkur   = new \DateTime($p['tanggal_ukur']);
        $umur      = $tglLahir->diff($tglUkur);
        $umurBulan = ($umur->y * 12) + $umur->m;
        return ['umur_bulan' => min($umurBulan, 60), 'tinggi_badan' => $p['tinggi_badan']];
    }, $pengukuran)) ?>;

    const tbAnakData = tbLabels.map((label, i) => {
        const umurBulan = tb[i].umur_bulan;
        const found = pengukuranTB.find(p => p.umur_bulan == umurBulan);
        return found ? found.tinggi_badan : null;
    });

    new Chart(document.getElementById('grafikKMSTB'), {
        type: 'line',
        data: {
            labels: tbLabels,
            datasets: [
                { label: 'TB Anak', data: tbAnakData, borderColor: '#1a3a6b', backgroundColor: '#1a3a6b', borderWidth: 2.5, borderDash: [6, 3], pointBackgroundColor: '#1a3a6b', pointRadius: 5, pointHoverRadius: 7, tension: 0.3, fill: false, spanGaps: false, order: 1 },
                { label: '+2 SD (Tinggi)', data: tbSD2pos, borderColor: '#2e7da0', borderWidth: 1.5, pointRadius: 0, tension: 0.3, fill: false, order: 2 },
                { label: 'Median (Normal)', data: tbMedian, borderColor: '#28A745', borderWidth: 2, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(40,167,69,0.1)', fill: '+1', order: 3 },
                { label: '-2 SD (Pendek)', data: tbSD2neg, borderColor: '#FFC107', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(255,193,7,0.15)', fill: '+1', order: 4 },
                { label: '-3 SD (Sangat Pendek)', data: tbSD3neg, borderColor: '#DC3545', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(220,53,69,0.15)', fill: 'origin', order: 5 }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true, padding: 15 } },
                tooltip: { callbacks: { label: function(context) { if (context.parsed.y !== null) { return context.dataset.label + ': ' + context.parsed.y + ' cm'; } return null; } } }
            },
            scales: {
                y: { beginAtZero: false, title: { display: true, text: 'Tinggi Badan (cm)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { title: { display: true, text: 'Umur (bulan)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { maxTicksLimit: 20 } }
            }
        }
    });
}

if (kmsBBTB.length > 0) {
    const _bbtbAnak = <?= json_encode(array_map(fn($p) => ['x' => (float) $p['tinggi_badan'], 'y' => (float) $p['berat_badan']], $pengukuran)) ?>;
    let _minT = Math.min(...kmsBBTB.map(d => parseFloat(d.tinggi)));
    let _maxT = Math.max(...kmsBBTB.map(d => parseFloat(d.tinggi)));
    if (_bbtbAnak.length > 0) {
        const _ts = _bbtbAnak.map(p => p.x);
        _minT = Math.max(_minT, Math.min(..._ts) - 8);
        _maxT = Math.min(_maxT, Math.max(..._ts) + 8);
    }
    const refBT = kmsBBTB.filter(d => parseFloat(d.tinggi) >= _minT && parseFloat(d.tinggi) <= _maxT);
    const _xy = key => refBT.map(d => ({ x: parseFloat(d.tinggi), y: parseFloat(d[key]) }));

    new Chart(document.getElementById('grafikKMSBBTB'), {
        type: 'line',
        data: {
            datasets: [
                { label: 'BB Anak', data: _bbtbAnak, showLine: false, borderColor: '#1a3a6b', backgroundColor: '#1a3a6b', pointBackgroundColor: '#1a3a6b', pointRadius: 6, pointHoverRadius: 8, order: 1 },
                { label: '+2 SD (Gizi Lebih)', data: _xy('sd2pos'), borderColor: '#17A2B8', borderWidth: 1.5, pointRadius: 0, tension: 0.3, fill: false, order: 2 },
                { label: 'Median (Ideal)', data: _xy('median'), borderColor: '#28A745', borderWidth: 2, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(40,167,69,0.1)', fill: '+1', order: 3 },
                { label: '-2 SD (Gizi Kurang)', data: _xy('sd2neg'), borderColor: '#FFC107', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(255,193,7,0.15)', fill: '+1', order: 4 },
                { label: '-3 SD (Gizi Buruk)', data: _xy('sd3neg'), borderColor: '#DC3545', borderWidth: 1.5, pointRadius: 0, tension: 0.3, backgroundColor: 'rgba(220,53,69,0.15)', fill: 'origin', order: 5 }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'nearest', intersect: false },
            plugins: {
                legend: { display: true, position: 'bottom', labels: { font: { size: 11 }, usePointStyle: true, padding: 15 } },
                tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y + ' kg @ ' + context.parsed.x + ' cm'; } } }
            },
            scales: {
                y: { beginAtZero: false, title: { display: true, text: 'Berat Badan (kg)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { type: 'linear', title: { display: true, text: 'Tinggi Badan (cm)', font: { size: 12 } }, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { maxTicksLimit: 20 } }
            }
        }
    });
}
</script>
<?php endif; ?>
<?= $this->endSection() ?>
