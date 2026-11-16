<?= $this->extend('layout/admin') ?>

<?= $this->section('head') ?>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h4>Edit Artikel</h4>
    <p>Ubah artikel dan informasi kesehatan</p>
</div>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="/admin/artikel/update/<?= esc($artikel['id'], 'url') ?>" method="POST" id="formArtikel">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" value="<?= esc(old('judul', $artikel['judul'] ?? ''), 'attr') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="">--Pilih Kategori--</option>
                        <option value="Gizi" <?= old('kategori', $artikel['kategori']) === 'Gizi' ? 'selected' : '' ?>>Gizi</option>
                        <option value="Tumbuh Kembang" <?= old('kategori', $artikel['kategori']) === 'Tumbuh Kembang' ? 'selected' : '' ?>>Tumbuh Kembang</option>
                        <option value="Imunisasi" <?= old('kategori', $artikel['kategori']) === 'Imunisasi' ? 'selected' : '' ?>>Imunisasi</option>
                        <option value="Kesehatan" <?= old('kategori', $artikel['kategori']) === 'Kesehatan' ? 'selected' : '' ?>>Kesehatan</option>
                        <option value="Lainnya" <?= old('kategori', $artikel['kategori']) === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="draft" <?= old('status', $artikel['status']) === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="publikasi" <?= old('status', $artikel['status']) === 'publikasi' ? 'selected' : '' ?>>Publikasi</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Konten Artikel <span class="text-danger">*</span></label>

                    <input type="hidden" name="konten" id="konten" value="<?= esc(old('konten', $artikel['konten'] ?? ''), 'attr') ?>">

                    <div id="editor" style="height: 350px;">
                        <?= old('konten', $artikel['konten'] ?? '') ?>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="/admin/artikel" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update Artikel</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ align: [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    const formArtikel = document.querySelector('#formArtikel');
    const kontenInput = document.querySelector('#konten');

    formArtikel.addEventListener('submit', function () {
        kontenInput.value = quill.root.innerHTML;
    });
</script>
<?= $this->endSection() ?>