<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Hasil Pengukuran</h4>
        <p>Data pengukuran semua anak per bulan</p>
    </div>
</div>

<!-- FILTER -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/anak/hasil-pengukuran" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bulan</label>
                <select name="bulan" class="form-control">
                    <?php
                    $namaBulan = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    foreach ($namaBulan as $val => $nama): ?>
                        <option value="<?= esc ($val, 'attr') ?>" <?= $bulan == $val ? 'selected' : '' ?>>
                            <?= esc($nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tahun</label>
                <select name="tahun" class="form-control">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= esc($y, 'attr') ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TABEL HASIL -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B">
            <i class="bi bi-table"></i>
            Hasil Pengukuran —
            <?php
            $namaBulan = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
            echo esc($namaBulan[$bulanKey] . ' ' . $tahun);
            ?>
        </h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary"><?= esc(count($pengukuran)) ?> data</span>
            <a href="/anak/export-pdf-pengukuran?bulan=<?= esc($bulan, 'url') ?>&tahun=<?= $tahun ?>" class="btn btn-danger btn-sm">
                <i class="bi bi-printer"></i> Export PDF
            </a>
            <a href="/anak/export-excel-pengukuran?bulan=<?= esc($bulan, 'url') ?>&tahun=<?= $tahun ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($pengukuran)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Ukur</th>
                        <th>NIK Anak</th>
                        <th>Nama Anak</th>
                        <th>JK</th>
                        <th>Umur</th>
                        <th>BB (kg)</th>
                        <th>PB/TB (cm)</th>
                        <th>LK (cm)</th>
                        <th>LiLA (cm)</th>
                        <th>N/T/O/B</th>
                        <th>Status BB/U</th>
                        <th>Status PB/U atau TB/U</th>
                        <th>Status BB/TB</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pengukuran as $p): ?>
                    <?php
                        // Hitung umur saat pengukuran
                        $tglLahir   = new DateTime($p['tanggal_lahir']);
                        $tglUkur    = new DateTime($p['tanggal_ukur']);
                        $umur       = $tglLahir->diff($tglUkur);
                        $umurStr    = $umur->y > 0 ? $umur->y . ' th ' . $umur->m . ' bln' : $umur->m . ' bln';
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc(date('d-m-Y', strtotime($p['tanggal_ukur']))) ?></td>
                        <td><?= esc($p['nik_anak']) ?></td>
                        <td>
                            <a href="/anak/detail/<?= $p['anak_id'] ?>" class="text-decoration-none fw-semibold">
                                <?= esc($p['nama']) ?>
                            </a>
                        </td>
                        <td><?= esc($p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L') ?></td>
                        <td><?= esc($umurStr) ?></td>
                        <td><?= esc($p['berat_badan']) ?></td>
                        <td><?= esc($p['tinggi_badan']) ?></td>
                        <td><?= esc($p['lingkar_kepala']) ?? '-' ?></td>
                        <td><?= esc($p['lila'] ?? '-') ?></td>
                        <td>
                            <?php
                            $ntob = $p['ntob'] ?? '';
                            $label = [
                                'N' => '<span class="badge bg-success">N</span>',
                                'T' => '<span class="badge bg-danger">T</span>',
                                'O' => '<span class="badge bg-secondary">O</span>',
                                'B' => '<span class="badge bg-info">B</span>',
                            ];
                            $ntob = (string) ($p['ntob'] ?? '');
                            echo $label[$ntob] ?? '-';
                            ?>
                        </td>
                        <td><?= esc($p['status_gizi'] ?? '-') ?></td>
                        <td>
                            <?php
                                helper('zscore');
                                $zt = $p['z_score_tb_u'] ?? null;
                                echo esc(($zt !== null && $zt !== '') ? statusPertumbuhanTBU((float) $zt) : '-');
                            ?>
                        </td>
                        <td>
                            <?php
                                $zbt = $p['z_score_bb_tb'] ?? null;
                                echo esc(($zbt !== null && $zbt !== '') ? statusGiziBBTB((float) $zbt) : '-');
                            ?>
                        </td>
                        <td><?= esc($p['keterangan'] ?? '-') ?></td>
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

<?= $this->endSection() ?>