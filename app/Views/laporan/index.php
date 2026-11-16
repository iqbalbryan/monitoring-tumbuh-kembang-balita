<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.chart-wrapper {
    position: relative;
    width: 100%;
}

.chart-wrapper-donut {
    height: 200px;
    min-height: 200px;
}

.chart-wrapper-bar {
    height: 230px;
    min-height: 230px;
}

.chart-wrapper canvas {
    display: block;
    width: 100% !important;
    height: 100% !important;
}
</style>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Laporan Bulanan</h4>
        <p>Rekap data posyandu per bulan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/laporan/export-pdf?bulan=<?= esc($bulan, 'url') ?>&tahun=<?= esc($tahun, 'url') ?>" class="btn btn-danger btn-sm">
            <i class="bi bi-printer"></i> Export PDF
        </a>
        <a href="/admin/laporan/export-excel?bulan=<?= esc($bulan, 'url') ?>&tahun=<?= esc($tahun, 'url') ?>" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
        </a>
    </div>
</div>

<!-- FILTER -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/laporan" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bulan</label>
                <select name="bulan" class="form-control">
                    <?php foreach ($namaBulanArr as $val => $nama): ?>
                        <option value="<?= esc($val, 'attr') ?>" <?= $bulan == $val ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-control">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= esc($y, 'attr') ?>" <?= $tahun == $y ? 'selected' : '' ?>>
                            <?= esc($y) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- RINGKASAN -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center h-100">
            <div class="fw-bold fs-3" style="color:#2F5E96"><?= esc($totalDitimbang) ?></div>
            <div class="text-muted small">Balita Ditimbang (D)</div>
            <div class="text-muted" style="font-size:.72rem">dari <?= esc($totalBalita) ?> balita aktif (S)</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center h-100">
            <div class="fw-bold fs-3" style="color:#2C7D5E"><?= esc($cakupanDS) ?>%</div>
            <div class="text-muted small">Cakupan Partisipasi (D/S)</div>
            <div class="text-muted" style="font-size:.72rem">target nasional &ge; 80%</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center h-100">
            <div class="fw-bold fs-3" style="color:#BB5747"><?= esc($anakBermasalah) ?></div>
            <div class="text-muted small">Anak Perlu Perhatian</div>
            <div class="text-muted" style="font-size:.72rem">underweight / stunting / wasting</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center h-100">
            <div class="fw-bold fs-3" style="color:#A9772A"><?= esc($kandidatLulus) ?></div>
            <div class="text-muted small">Kandidat Lulus</div>
            <div class="text-muted" style="font-size:.72rem">usia &ge; 60 bulan</div>
        </div>
    </div>
</div>

<!-- GRAFIK 3 INDIKATOR ANTROPOMETRI -->
<div class="row g-4 mb-4">
    <!-- BB/U -->
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-egg-fried"></i> Berat Badan / Umur (BB/U)</h6>
                <small class="text-muted">Indikator berat badan (underweight)</small>
            </div>
            <div class="card-body">
                <div class="chart-wrapper chart-wrapper-donut">
                    <canvas id="grafikGizi"></canvas>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#EAF6F0">
                            <span>BB Normal</span><strong><?= esc($rekapGizi['BB Normal'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBF3E1">
                            <span>BB Kurang</span><strong><?= esc($rekapGizi['BB Kurang']?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBECE8">
                            <span>BB Sangat Kurang</span><strong><?= esc($rekapGizi['BB Sangat Kurang'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#E9F4F7">
                            <span>Risiko BB Lebih</span><strong><?= esc($rekapGizi['Risiko BB Lebih'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TB/U (Stunting) -->
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-rulers"></i> Panjang/Tinggi Badan / Umur (PB/U atau TB/U)</h6>
                <small class="text-muted">Indikator stunting (pendek)</small>
            </div>
            <div class="card-body">
                <div class="chart-wrapper chart-wrapper-donut">
                    <canvas id="grafikTBU"></canvas>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#EAF6F0">
                            <span>Normal</span><strong><?= esc($rekapTBU['Normal'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBF3E1">
                            <span>Pendek</span><strong><?= esc($rekapTBU['Pendek'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBECE8">
                            <span>Sangat Pendek</span><strong><?=esc($rekapTBU['Sangat Pendek'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#E9F4F7">
                            <span>Tinggi</span><strong><?= esc($rekapTBU['Tinggi'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BB/TB (Wasting) -->
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-droplet-half"></i> Berat Badan / Tinggi (BB/TB)</h6>
                <small class="text-muted">Indikator wasting (gizi kurang/buruk)</small>
            </div>
            <div class="card-body">
                <div class="chart-wrapper chart-wrapper-donut">
                    <canvas id="grafikBBTB"></canvas>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#EAF6F0">
                            <span>Gizi Baik</span><strong><?= esc($rekapBBTB['Gizi Baik'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#F0F6E8">
                            <span>Berisiko Gizi Lebih</span><strong><?= esc($rekapBBTB['Berisiko Gizi Lebih'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBF3E1">
                            <span>Gizi Lebih</span><strong><?= esc($rekapBBTB['Gizi Lebih'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#F7EDE3">
                            <span>Obesitas</span><strong><?= esc($rekapBBTB['Obesitas'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBECE8">
                            <span>Gizi Kurang</span><strong><?= esc($rekapBBTB['Gizi Kurang'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#F7E0DD">
                            <span>Gizi Buruk</span><strong><?= esc($rekapBBTB['Gizi Buruk'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GRAFIK KEHADIRAN N/T/O/B -->
<div class="row g-4 mb-4">
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-clipboard-check"></i> Rekap Penimbangan (N/T/O/B)</h6>
            </div>
            <div class="card-body">
                <div class="chart-wrapper chart-wrapper-bar">
                    <canvas id="grafikNTOB"></canvas>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#EAF6F0">
                            <span>N - Naik</span><strong><?= esc($rekapNTOB['N'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#FBECE8">
                            <span>T - Tidak Naik</span><strong><?= esc($rekapNTOB['T'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#F0F2F6">
                            <span>O - Tidak Ditimbang Bln Lalu</span><strong><?= esc($rekapNTOB['O'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between small p-2 rounded" style="background:#E9F4F7">
                            <span>B - Baru</span><strong><?= esc($rekapNTOB['B'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-info-circle"></i> Keterangan Istilah</h6>
            </div>
            <div class="card-body small text-muted">
                <p class="mb-2"><strong>N/T/O/B</strong> menilai tren berat badan terhadap bulan sebelumnya:</p>
                <ul class="mb-3 ps-3">
                    <li><strong>N</strong> &mdash; Berat <em>Naik</em> sesuai garis pertumbuhan.</li>
                    <li><strong>T</strong> &mdash; Berat <em>Tidak Naik</em>.</li>
                    <li><strong>O</strong> &mdash; Anak ditimbang bulan ini, tetapi <em>tidak ditimbang bulan lalu</em> sehingga tren N/T belum bisa dihitung.</li>
                    <li><strong>B</strong> &mdash; Anak <em>Baru</em> pertama kali ditimbang.</li>
                </ul>
                <p class="mb-0"><strong>D/S</strong> = jumlah balita ditimbang dibanding seluruh balita aktif &mdash; mengukur tingkat partisipasi posyandu.</p>
            </div>
        </div>
    </div>
</div>

<!-- TABEL DETAIL -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B">
            <i class="bi bi-table"></i> Detail Pengukuran &mdash; <?= esc($namaBulan) ?>
        </h6>
    </div>
    <div class="card-body">
        <?php if (!empty($pengukuran)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tgl Ukur</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Umur</th>
                        <th>BB (kg)</th>
                        <th>PB atau TB (cm)</th>
                        <th>LiLA</th>
                        <th>N/T/O/B</th>
                        <th>Z BB/U</th>
                        <th>Status Gizi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pengukuran as $p): ?>
                    <?php
                        $tglLahir  = new DateTime($p['tanggal_lahir']);
                        $tglUkur   = new DateTime($p['tanggal_ukur']);
                        $umur      = $tglLahir->diff($tglUkur);
                        $umurBulan = ($umur->y * 12) + $umur->m;
                        $warnaGizi = [
                            'BB Normal'        => 'bg-success',
                            'BB Kurang'        => 'bg-warning text-dark',
                            'BB Sangat Kurang' => 'bg-danger',
                            'Risiko BB Lebih'  => 'bg-info',
                        ];
                        $warnaNtob = [
                            'N' => 'bg-success',
                            'T' => 'bg-danger',
                            'O' => 'bg-secondary',
                            'B' => 'bg-info'
                        ];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc(date('d-m-Y', strtotime($p['tanggal_ukur']))) ?></td>
                        <td class="fw-semibold"><?= esc($p['nama']) ?></td>
                        <td><?= esc($p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L') ?></td>
                        <td><?= esc($umurBulan) ?> bln</td>
                        <td><?= esc($p['berat_badan']) ?></td>
                        <td><?= esc($p['tinggi_badan']) ?></td>
                        <td><?= esc($p['lila'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($p['ntob'])): ?>
                                <span
                                    class="badge <?= esc($warnaNtob[$p['ntob']] ?? 'bg-secondary', 'attr') ?>"
                                    title="<?= esc($p['ntob'] === 'O' ? 'Tidak ditimbang bulan lalu' : '', 'attr') ?>"
                                >
                                    <?= esc($p['ntob']) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= esc($p['z_score_bb_u'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($p['status_gizi'])): ?>
                                <span class="badge <?= esc($warnaGizi[$p['status_gizi']] ?? 'bg-secondary', 'attr') ?>">
                                    <?= esc($p['status_gizi']) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-clipboard-x" style="font-size:3rem;"></i>
                <p class="mt-2">Tidak ada data pengukuran untuk bulan ini</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const donutOpts = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '62%',
    animation: {
        duration: 0
    },
    animations: false,
    resizeDelay: 0,
    plugins: {
        legend: {
            display: false
        }
    }
};

// BB/U
window.chartGizi = new Chart(document.getElementById('grafikGizi'), {
    type: 'doughnut',
    data: {
        labels: ['BB Normal', 'BB Kurang', 'BB Sangat Kurang', 'Risiko BB Lebih'],
        datasets: [{
            data: [
                <?= (int) ($rekapGizi['BB Normal'] ?? 0) ?>,
                <?= (int) ($rekapGizi['BB Kurang'] ?? 0) ?>,
                <?= (int) ($rekapGizi['BB Sangat Kurang'] ?? 0) ?>,
                <?= (int) ($rekapGizi['Risiko BB Lebih'] ?? 0) ?>
            ],
            backgroundColor: ['#5FB58C', '#E6C06B', '#E08571', '#6FB0C6'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: donutOpts
});

// PB/U atau TB/U (stunting)
window.chartTBU = new Chart(document.getElementById('grafikTBU'), {
    type: 'doughnut',
    data: {
        labels: ['Normal', 'Pendek', 'Sangat Pendek', 'Tinggi'],
        datasets: [{
            data: [
                <?= (int) ($rekapTBU['Normal'] ?? 0) ?>,
                <?= (int) ($rekapTBU['Pendek'] ?? 0) ?>,
                <?= (int) ($rekapTBU['Sangat Pendek'] ?? 0) ?>,
                <?= (int) ($rekapTBU['Tinggi'] ?? 0) ?>
            ],
            backgroundColor: ['#5FB58C', '#E6C06B', '#E08571', '#6FB0C6'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: donutOpts
});

// BB/TB (wasting)
window.chartBBTB = new Chart(document.getElementById('grafikBBTB'), {
    type: 'doughnut',
    data: {
        labels: ['Gizi Baik', 'Berisiko Gizi Lebih', 'Gizi Lebih', 'Obesitas', 'Gizi Kurang', 'Gizi Buruk'],
        datasets: [{
            data: [
                <?= (int) ($rekapBBTB['Gizi Baik'] ?? 0) ?>,
                <?= (int) ($rekapBBTB['Berisiko Gizi Lebih'] ?? 0) ?>,
                <?= (int) ($rekapBBTB['Gizi Lebih'] ?? 0) ?>,
                <?= (int) ($rekapBBTB['Obesitas'] ?? 0) ?>,
                <?= (int) ($rekapBBTB['Gizi Kurang'] ?? 0) ?>,
                <?= (int) ($rekapBBTB['Gizi Buruk'] ?? 0) ?>
            ],
            backgroundColor: ['#5FB58C', '#9CC97F', '#E6C06B', '#D98C6B', '#E08571', '#C25E5E'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: donutOpts
});

// N/T/O/B
window.chartNTOB = new Chart(document.getElementById('grafikNTOB'), {
    type: 'bar',
    data: {
        labels: ['N - Naik', 'T - Tidak Naik', 'O - Tdk Ditimbang Bln Lalu', 'B - Baru'],
        datasets: [{
            data: [
                <?= (int) ($rekapNTOB['N'] ?? 0) ?>,
                <?= (int) ($rekapNTOB['T'] ?? 0) ?>,
                <?= (int) ($rekapNTOB['O'] ?? 0) ?>,
                <?= (int) ($rekapNTOB['B'] ?? 0) ?>
            ],
            backgroundColor: ['#5FB58C', '#E08571', '#A7B2C2', '#6FB0C6'],
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
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});
window.addEventListener('load', function () {
    setTimeout(function () {
        [
            window.chartGizi,
            window.chartTBU,
            window.chartBBTB,
            window.chartNTOB
        ].forEach(function(chart) {
            if (chart) {
                chart.resize();
                chart.update('none');
            }
        });
    }, 800);
});
</script>

<?= $this->endSection() ?>
