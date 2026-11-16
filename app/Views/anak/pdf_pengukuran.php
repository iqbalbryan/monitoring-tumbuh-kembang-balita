<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hasil Pengukuran</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h3 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1A3A6B; color: white; padding: 5px; text-align: left; font-size: 9px; }
        td { padding: 4px 5px; border-bottom: 1px solid #ddd; font-size: 9px; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .badge-n { color: green; font-weight: bold; }
        .badge-t { color: red; font-weight: bold; }
        .badge-o { color: gray; font-weight: bold; }
        .badge-b { color: blue; font-weight: bold; }
    </style>
</head>
<body>
    <h3>HASIL PENGUKURAN POSYANDU NUSA INDAH</h3>
    <p>Bulan: <?= esc($namaBulan) ?></p>
    <p>Dicetak pada: <?= esc(date('d-m-Y H:i')) ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Ukur</th>
                <th>NIK</th>
                <th>Nama Anak</th>
                <th>JK</th>
                <th>Umur</th>
                <th>BB (kg)</th>
                <th>PB/TB (cm)</th>
                <th>LK (cm)</th>
                <th>LiLA (cm)</th>
                <th>N/T/O/B</th>
                <th>Z BB/U</th>
                <th>Z PB/TB/U</th>
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
                $ntobClass = ['N' => 'badge-n', 'T' => 'badge-t', 'O' => 'badge-o', 'B' => 'badge-b'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc(date('d-m-Y', strtotime($p['tanggal_ukur']))) ?></td>
                <td><?= esc($p['nik_anak']) ?></td>
                <td><?= esc($p['nama']) ?></td>
                <td><?= esc($p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L') ?></td>
                <td><?= esc($umurBulan) ?> bln</td>
                <td><?= esc($p['berat_badan']) ?></td>
                <td><?= esc($p['tinggi_badan']) ?></td>
                <td><?= esc($p['lingkar_kepala'] ?? '-') ?></td>
                <td><?= esc($p['lila'] ?? '-') ?></td>
                <td class="<?= esc($ntobClass[$p['ntob'] ?? ''] ?? '', 'attr') ?>">
                    <?= esc($p['ntob'] ?? '-') ?>
                </td>
                <td><?= esc($p['z_score_bb_u'] ?? '-') ?></td>
                <td><?= esc($p['z_score_tb_u'] ?? '-') ?></td>
                <td><?= esc($p['status_gizi'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>