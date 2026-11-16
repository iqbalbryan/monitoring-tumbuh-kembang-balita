<?= $this->extend('orangtua/layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h6 class="pos-section-title"><i class="bi bi-key"></i> Ganti Password</h6>
    <p class="text-secondary small mb-0">Password awal akun kamu sama dengan NIK. Kamu bisa menggantinya di sini.</p>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" style="border-radius:14px">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="pos-card" style="max-width:480px">
    <div class="card-body">
        <form action="/orangtua/ganti-password" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Password lama</label>
                <input type="password" name="password_lama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password baru</label>
                <input type="password" name="password_baru" class="form-control" minlength="8" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ulangi password baru</label>
                <input type="password" name="konfirmasi_password" class="form-control" minlength="8" required>
            </div>
            <button type="submit" class="btn text-white" style="background:var(--pos-primary)">Simpan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
