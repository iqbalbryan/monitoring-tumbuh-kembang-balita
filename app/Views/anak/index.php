<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4>Data Anak</h4>
        <p>Kelola data anak yang terdaftar di Posyandu Nusa Indah</p>
    </div>
    <div class="d-flex gap-2">
        <!-- <a href="/anak/import" class="btn btn-warning btn-sm"><i class="bi bi-file-earmark-arrow-up"></i> Import Excel</a> -->
        <a href="/anak/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Data</a>
    </div>
</div>

<?php
    $buildQs = function ($s) use ($keyword, $jk) {
        $q = '/anak?status=' . $s;
        if (!empty($keyword)) $q .= '&keyword=' . urlencode($keyword);
        if (!empty($jk)) $q .= '&jk=' . urlencode($jk);
        return $q;
    };
?>

<!-- TAB AKTIF / ARSIP -->
<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $status === 'aktif' ? 'active' : '' ?>" href="<?= $buildQs('aktif') ?>">
            <i class="bi bi-people-fill"></i> Aktif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'kandidat' ? 'active' : '' ?>" href="<?= $buildQs('kandidat') ?>">
            <i class="bi bi-mortarboard-fill"></i> Kandidat Lulus
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $status === 'arsip' ? 'active' : '' ?>" href="<?= $buildQs('arsip') ?>">
            <i class="bi bi-archive"></i> Arsip (Lulus/Pindah/Lainnya)
        </a>
    </li>
</ul>

<!-- SEARCH & FILTER -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="/anak" class="row g-3 align-items-end">
            <input type="hidden" name="status" value="<?= esc($status, 'attr') ?>">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Cari</label>
                <input type="text" name="keyword" class="form-control" placeholder="Cari nama, NIK, nama ibu, nama ayah..." value="<?= esc($keyword, 'attr') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Kelamin</label>
                <select name="jk" class="form-control">
                    <option value="">-- Semua --</option>
                    <option value="Laki-laki" <?= $jk === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $jk === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="<?= $buildQs($status) ?>" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc (session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc (session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIK Anak</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Lahir</th>
                        <th>Nama Ibu</th>
                        <th>Keaktifan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($anak)): ?>
                        <?php $no = 1; foreach ($anak as $row): ?>
                        <?php
                            $sk = $row['status_keaktifan'] ?? 'Aktif';
                            $skClass = $sk === 'Aktif' ? 'bg-success'
                                : ($sk === 'Lulus' ? 'bg-primary'
                                : ($sk === 'Pindah' ? 'bg-info text-dark' : 'bg-secondary'));
                            $umurBln = 0;
                            if (!empty($row['tanggal_lahir'])) {
                                $d = date_diff(date_create($row['tanggal_lahir']), date_create('now'));
                                $umurBln = ($d->y * 12) + $d->m;
                            }
                            $kandidatLulus = ($sk === 'Aktif' && $umurBln >= 60);
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc ($row['nik_anak']) ?></td>
                            <td><?= esc ($row['nama']) ?></td>
                            <td><?= esc ($row['jenis_kelamin']) ?></td>
                            <td><?= esc (date('d-m-Y', strtotime($row['tanggal_lahir']))) ?></td>
                            <td><?= esc ($row['nama_ibu']) ?></td>
                            <td>
                                <span class="badge <?= esc ($skClass, 'attr') ?>"><?= $sk ?></span>
                                <?php if ($kandidatLulus): ?>
                                    <span class="badge bg-warning text-dark" title="Sudah lebih dari 5 tahun, kandidat lulus"><i class="bi bi-mortarboard-fill"></i> Kandidat Lulus</span>
                                    <div class="small text-muted"><?= esc (floor($umurBln / 12)) ?> th <?= esc ($umurBln % 12) ?> bln</div>
                                <?php endif; ?>
                                <?php if ($sk !== 'Aktif' && !empty($row['tanggal_status'])): ?>
                                    <div class="small text-muted"><?= esc (date('d-m-Y', strtotime($row['tanggal_status']))) ?></div>
                                <?php endif; ?>
                                <?php if ($sk !== 'Aktif' && !empty($row['alasan_status'])): ?>
                                    <div class="small text-muted fst-italic"><?= esc($row['alasan_status']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                <a href="/anak/detail/<?= esc ($row['id'], 'url') ?>" class="btn btn-info btn-sm text-white">Detail</a>
                                <button type="button" class="btn btn-dark btn-sm btn-status"
                                    data-bs-toggle="modal" data-bs-target="#modalStatus"
                                    data-id="<?= esc ($row['id'], 'attr') ?>"
                                    data-nama="<?= esc($row['nama'], 'attr') ?>"
                                    data-status="<?= esc($sk, 'attr') ?>"
                                    data-tanggal="<?= esc($row['tanggal_status'] ?? '', 'attr') ?>"
                                    data-alasan="<?= esc($row['alasan_status'] ?? '', 'attr') ?>"
                                    data-kandidat="<?= $kandidatLulus ? '1' : '0' ?>">
                                    <i class="bi bi-arrow-left-right"></i> Status
                                </button>
                                <a href="/anak/edit/<?= esc ($row['id'], 'url') ?>" class="btn btn-warning btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <?= $status === 'arsip' ? 'Belum ada anak di arsip.' : ($status === 'kandidat' ? 'Tidak ada anak yang menjadi kandidat lulus.' : 'Belum ada data anak aktif.') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL UBAH STATUS -->
<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formStatus">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> Ubah Status Keaktifan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Anak: <strong id="modalNama"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Keaktifan</label>
                        <select name="status_keaktifan" id="modalSelectStatus" class="form-control" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Lulus">Lulus (sudah ≥ 5 tahun)</option>
                            <option value="Pindah">Pindah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="tanggal_status" id="modalTanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan / Catatan</label>
                        <textarea name="alasan_status" id="modalAlasan" class="form-control" rows="2" placeholder="mis. pindah ke luar kota, lulus karena usia, dll"></textarea>
                    </div>
                    <div class="alert alert-light border small mb-0">
                        <i class="bi bi-info-circle"></i> Memilih <strong>Aktif</strong> akan mengembalikan anak ke daftar aktif (tanggal & alasan dikosongkan).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-status').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('formStatus').action = '/anak/ubah-status/' + this.dataset.id;
            document.getElementById('modalNama').textContent = this.dataset.nama || '';
            var curStatus = this.dataset.status || 'Aktif';
            var curAlasan = this.dataset.alasan || '';
            if (this.dataset.kandidat === '1' && curStatus === 'Aktif') {
                curStatus = 'Lulus';
                if (!curAlasan) curAlasan = 'Sudah berusia 5 tahun atau lebih (lulus posyandu)';
            }
            document.getElementById('modalSelectStatus').value = curStatus;
            document.getElementById('modalTanggal').value = this.dataset.tanggal || '<?= date('Y-m-d') ?>';
            document.getElementById('modalAlasan').value = curAlasan;
        });
    });
</script>

<?= $this->endSection() ?>
