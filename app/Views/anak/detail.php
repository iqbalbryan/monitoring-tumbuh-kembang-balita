<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Detail Anak</h4>
        <p><?= esc ($anak['nama']) ?></p>
    </div>
    <a href="/anak" class="btn btn-secondary btn-sm">← Kembali</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc (session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<!-- INFO ANAK -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-person-circle"></i> Informasi Anak</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">NIK Anak</div>
                <div class="fw-bold"><?= esc ($anak['nik_anak']) ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Nama</div>
                <div class="fw-bold"><?= esc ($anak['nama']) ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Jenis Kelamin</div>
                <div class="fw-bold"><?= esc ($anak['jenis_kelamin']) ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Tanggal Lahir</div>
                <div class="fw-bold"><?= esc (date('d-m-Y', strtotime($anak['tanggal_lahir']))) ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Berat Lahir</div>
                <div class="fw-bold"><?= esc (($anak['berat_badan_lahir'] && $anak['berat_badan_lahir'] > 0) ? $anak['berat_badan_lahir'] . ' kg' : '-' )?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Panjang Lahir</div>
                <div class="fw-bold"><?= esc (($anak['panjang_badan_lahir'] && $anak['panjang_badan_lahir'] > 0) ? $anak['panjang_badan_lahir'] . ' cm' : '-' )?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Lingkar Kepala Lahir</div>
                <div class="fw-bold"><?= esc (($anak['lingkar_kepala_lahir'] && $anak['lingkar_kepala_lahir'] > 0) ? $anak['lingkar_kepala_lahir'] . ' cm' : '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Status Lahir</div>
                <div class="fw-bold"><?= esc ($anak['premature'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Nama Ibu</div>
                <div class="fw-bold"><?= esc ($anak['nama_ibu'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">NIK Ibu</div>
                <div class="fw-bold"><?= esc (!empty($anak['nik_ibu']) ? $anak['nik_ibu'] : '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">Nama Ayah</div>
                <div class="fw-bold"><?= esc ($anak['nama_ayah'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">NIK Ayah</div>
                <div class="fw-bold"><?= esc (!empty($anak['nik_ayah']) ? $anak['nik_ayah'] : '-') ?></div>
            </div>
            <div class="col-md-3">
                <div style="font-size:0.8rem;color:#888">No KK</div>
                <div class="fw-bold"><?= esc ($anak['no_kk'] ?? '-') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- RIWAYAT PENGUKURAN -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold" style="color:#1A3A6B"><i class="bi bi-graph-up"></i> Riwayat Pengukuran</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($pengukuran)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal Ukur</th>
                        <th>Berat (kg)</th>
                        <th>Tinggi (cm)</th>
                        <th>Lingkar Kepala (cm)</th>
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
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc (date('d-m-Y', strtotime($p['tanggal_ukur']))) ?></td>
                        <td><?= esc ($p['berat_badan']) ?></td>
                        <td><?= esc ($p['tinggi_badan']) ?></td>
                        <td><?= esc ($p['lingkar_kepala'] ?? '-') ?></td>
                        <td><?= esc ($p['lila'] ?? '-') ?></td>
                        <td>
                            <?php
                            $ntob = $p['ntob'] ?? '';
                            $label = [
                                'N' => '<span class="badge bg-success">N - Naik</span>',
                                'T' => '<span class="badge bg-danger">T - Tidak Naik</span>',
                                'O' => '<span class="badge bg-secondary" title="Tidak ditimbang bulan lalu">O - Tdk Ditimbang Bln Lalu</span>',
                                'B' => '<span class="badge bg-info">B - Baru</span>',
                            ];
                            $ntob = $p['ntob'] ?? '';
                            echo $label[$ntob] ?? '-';
                            ?>
                        </td>
                        <td><?= esc ($p['status_gizi'] ?? '-') ?></td>
                        <td>
                            <?php
                                helper('zscore');
                                $zt = $p['z_score_tb_u'] ?? null;
                                echo esc(($zt !== null && $zt !== '') ? statusPertumbuhanTBU((float) $zt) : '-');
                            ?>
                        </td>
                        <td>
                            <?php
                                helper('zscore');
                                $zbt = $p['z_score_bb_tb'] ?? null;
                                echo esc(($zbt !== null && $zbt !== '') ? statusGiziBBTB((float) $zbt) : '-');
                            ?>
                        </td>
                        <td><?= esc ($p['keterangan'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="bi bi-clipboard-x" style="font-size:3rem;"></i>
                <p class="mt-2">Belum ada data pengukuran</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>