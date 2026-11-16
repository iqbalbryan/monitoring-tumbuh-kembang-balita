<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 14px; }
        .header p { margin: 3px 0; font-size: 10px; color: #666; }
        .rekap { display: flex; gap: 10px; margin-bottom: 15px; }
        .rekap-box { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 8px; text-align: center; }
        .rekap-box .nilai { font-size: 18px; font-weight: bold; color: #1A3A6B; }
        .rekap-box .label { font-size: 9px; color: #666; }
        .section-title { font-weight: bold; font-size: 11px; color: #1A3A6B; margin: 12px 0 6px; border-bottom: 2px solid #5BC8F5; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #1A3A6B; color: white; padding: 5px; text-align: left; font-size: 9px; }
        td { padding: 4px 5px; border-bottom: 1px solid #eee; font-size: 9px; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .badge-baik { color: #2C7D5E; font-weight: bold; }
        .badge-kurang { color: #A9772A; font-weight: bold; }
        .badge-buruk { color: #BB5747; font-weight: bold; }
        .badge-lebih { color: #2C7186; font-weight: bold; }
        .ind-table { width: 32%; display: inline-table; vertical-align: top; margin-right: 1%; }
        .note { font-size: 8.5px; color: #777; margin-top: 4px; }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN BULANAN POSYANDU NUSA INDAH</h3>
        <p>Bulan: <?= esc($namaBulan) ?> | Dicetak: <?= esc(date('d-m-Y H:i')) ?></p>
    </div>

    <!-- RINGKASAN -->
    <div class="rekap">
        <div class="rekap-box">
            <div class="nilai"><?= esc($totalDitimbang) ?> / <?= esc($totalBalita) ?></div>
            <div class="label">Balita Ditimbang (D/S)</div>
        </div>
        <div class="rekap-box">
            <div class="nilai"><?= esc($cakupanDS) ?>%</div>
            <div class="label">Cakupan Partisipasi (D/S)</div>
        </div>
        <div class="rekap-box">
            <div class="nilai" style="color:#BB5747"><?= esc($anakBermasalah) ?></div>
            <div class="label">Anak Perlu Perhatian</div>
        </div>
        <div class="rekap-box">
            <div class="nilai" style="color:#A9772A"><?= esc($kandidatLulus) ?></div>
            <div class="label">Kandidat Lulus (&ge;60 bln)</div>
        </div>
    </div>

    <!-- REKAP 3 INDIKATOR ANTROPOMETRI -->
    <div class="section-title">Rekap Status Gizi (3 Indikator)</div>
    <table class="ind-table">
        <thead>
            <tr><th>BB/U (Underweight)</th><th>Jml</th></tr>
        </thead>
        <tbody>
            <tr><td>BB Normal</td><td class="badge-baik"><?= esc($rekapGizi['BB Normal'] ?? 0) ?></td></tr>
            <tr><td>BB Kurang</td><td class="badge-kurang"><?= esc($rekapGizi['BB Kurang'] ?? 0) ?></td></tr>
            <tr><td>BB Sangat Kurang</td><td class="badge-buruk"><?= esc($rekapGizi['BB Sangat Kurang'] ?? 0) ?></td></tr>
            <tr><td>Risiko BB Lebih</td><td class="badge-lebih"><?= esc($rekapGizi['Risiko BB Lebih'] ?? 0) ?></td></tr>
        </tbody>
    </table>
    <table class="ind-table">
        <thead>
            <tr><th>PB/TB/U (Stunting)</th><th>Jml</th></tr>
        </thead>
        <tbody>
            <tr><td>Normal</td><td class="badge-baik"><?= esc($rekapTBU['Normal'] ?? 0) ?></td></tr>
            <tr><td>Pendek</td><td class="badge-kurang"><?= esc($rekapTBU['Pendek'] ?? 0) ?></td></tr>
            <tr><td>Sangat Pendek</td><td class="badge-buruk"><?= esc($rekapTBU['Sangat Pendek'] ?? 0) ?></td></tr>
            <tr><td>Tinggi</td><td class="badge-lebih"><?= esc($rekapTBU['Tinggi'] ?? 0) ?></td></tr>
        </tbody>
    </table>
    <table class="ind-table" style="margin-right:0">
        <thead>
            <tr><th>BB/TB (Wasting)</th><th>Jml</th></tr>
        </thead>
        <tbody>
            <tr><td>Gizi Baik</td><td class="badge-baik"><?= esc($rekapBBTB['Gizi Baik'] ?? 0) ?></td></tr>
            <tr><td>Gizi Kurang</td><td class="badge-kurang"><?= esc($rekapBBTB['Gizi Kurang'] ?? 0) ?></td></tr>
            <tr><td>Gizi Buruk</td><td class="badge-buruk"><?= esc($rekapBBTB['Gizi Buruk'] ?? 0) ?></td></tr>
            <tr><td>Gizi Lebih / Risiko</td><td class="badge-lebih"><?= esc(($rekapBBTB['Gizi Lebih'] ?? 0) + ($rekapBBTB['Berisiko Gizi Lebih'] ?? 0)) ?></td></tr>
            <tr><td>Obesitas</td><td class="badge-lebih"><?= esc($rekapBBTB['Obesitas'] ?? 0) ?></td></tr>
        </tbody>
    </table>

    <!-- REKAP PENIMBANGAN N/T/O/B -->
    <div class="section-title">Rekap Penimbangan (N/T/O/B)</div>
    <table style="width:60%">
        <thead>
            <tr><th>Keterangan</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            <tr><td>N - Naik</td><td class="badge-baik"><?= esc($rekapNTOB['N'] ?? 0) ?></td></tr>
            <tr><td>T - Tidak Naik</td><td class="badge-buruk"><?= esc($rekapNTOB['T'] ?? 0) ?></td></tr>
            <tr><td>O - Tidak Ditimbang Bulan Lalu</td><td><?= esc($rekapNTOB['O'] ?? 0) ?></td></tr>
            <tr><td>B - Baru</td><td class="badge-lebih"><?= esc($rekapNTOB['B'] ?? 0) ?></td></tr>
        </tbody>
    </table>
    <div class="note">Keterangan: <strong>O</strong> = anak ditimbang bulan ini tetapi tidak ditimbang bulan lalu, sehingga tren naik/tidak naik belum dapat dihitung (bukan berarti tidak hadir).</div>

    <!-- DETAIL PENGUKURAN -->
    <div class="section-title">Detail Pengukuran</div>
    <table>
        <thead>
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
                $tglLahir  = new \DateTime($p['tanggal_lahir']);
                $tglUkur   = new \DateTime($p['tanggal_ukur']);
                $umur      = $tglLahir->diff($tglUkur);
                $umurBulan = ($umur->y * 12) + $umur->m;
                $warnaGizi = [
                    'BB Normal'        => 'badge-baik',
                    'BB Kurang'        => 'badge-kurang',
                    'BB Sangat Kurang' => 'badge-buruk',
                    'Risiko BB Lebih'  => 'badge-lebih',
                ];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc(date('d-m-Y', strtotime($p['tanggal_ukur']))) ?></td>
                <td><?= esc($p['nama']) ?></td>
                <td><?= esc($p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L') ?></td>
                <td><?= esc($umurBulan) ?> bln</td>
                <td><?= esc($p['berat_badan']) ?></td>
                <td><?= esc($p['tinggi_badan']) ?></td>
                <td><?= esc($p['lila'] ?? '-') ?></td>
                <td><?= esc($p['ntob'] ?? '-') ?></td>
                <td><?= esc($p['z_score_bb_u'] ?? '-') ?></td>
                <td class="<?= esc($warnaGizi[$p['status_gizi'] ?? ''] ?? '', 'attr') ?>">
                    <?= esc($p['status_gizi'] ?? '-') ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
