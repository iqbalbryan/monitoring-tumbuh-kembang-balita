<?= $this->extend('orangtua/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="pos-section-title"><i class="bi bi-bell"></i> Notifikasi</h6>
        <p class="text-secondary small mb-0">Semua notifikasi dan pemberitahuan</p>
    </div>
    <?php if (!empty($notifikasi)): ?>
    <form action="/orangtua/baca-notifikasi" method="post" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm text-white" style="background:var(--pos-primary)">
            <i class="bi bi-check-all"></i> Tandai Semua Dibaca
        </button>
    </form>
    <?php endif; ?>
</div>

<div class="pos-card">
    <div class="card-body p-4">
        <?php if (!empty($notifikasi)): ?>
            <?php foreach ($notifikasi as $n): ?>
            <?php
                $tipeMap = [
                    'jadwal'      => ['icon' => 'bi-calendar-event', 'bg' => '#e7f0fb', 'fg' => '#2563a8'],
                    'pengukuran'  => ['icon' => 'bi-clipboard2-pulse', 'bg' => '#e3f5ee', 'fg' => '#1f7a5f'],
                    'status_gizi' => ['icon' => 'bi-exclamation-triangle', 'bg' => '#fdf0dd', 'fg' => '#b9740f'],
                    'info'        => ['icon' => 'bi-info-circle', 'bg' => '#e7f0fb', 'fg' => '#2563a8'],
                ];
                $t = $tipeMap[$n['tipe']] ?? $tipeMap['info'];
            ?>
            <div class="pos-list-item" style="<?= empty($n['is_read']) ? 'background:#f5f9ff;border-radius:12px;padding:12px;margin-bottom:6px;border-bottom:none;' : '' ?>">
                <div class="pos-list-ico" style="background:<?= esc($t['bg'], 'attr') ?>;color:<?= esc($t['fg'], 'attr') ?>"><i class="bi <?= esc($t['icon'], 'attr') ?>"></i></div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="fw-semibold" style="color:var(--pos-primary-dark)"><?= esc($n['judul']) ?></div>
                        <?php if (empty($n['is_read'])): ?>
                            <span class="pos-badge" style="background:var(--pos-primary-soft);color:var(--pos-primary)">Baru</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-secondary small mt-1"><?= esc($n['pesan']) ?></div>
                    <div class="text-secondary mt-1" style="font-size:.72rem"><i class="bi bi-clock"></i> <?= esc(tglIndo($n['created_at'], true)) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="pos-empty"><div class="ico"><i class="bi bi-bell-slash"></i></div><p class="mt-3 mb-0">Belum ada notifikasi</p></div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
