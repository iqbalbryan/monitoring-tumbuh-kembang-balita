<?php

/**
 * Helper Z-Score WHO
 * Menghitung Z-Score dan Status Gizi berdasarkan standar WHO
 */

/**
 * Hitung umur dalam bulan
 */
function hitungUmurBulan($tanggalLahir, $tanggalUkur)
{
    $lahir = new DateTime($tanggalLahir);
    $ukur  = new DateTime($tanggalUkur);
    $diff  = $lahir->diff($ukur);
    return ($diff->y * 12) + $diff->m;
}

/**
 * Hitung Z-Score BB/U (Berat Badan per Umur)
 */
function hitungZScoreBBU($beratBadan, $umurBulan, $jenisKelamin, $db)
{
    $jk = $jenisKelamin === 'Laki-laki' ? 'L' : 'P';

    $data = $db->table('who_bb_u')
        ->where('jenis_kelamin', $jk)
        ->where('umur_bulan', $umurBulan)
        ->get()
        ->getRowArray();

    if (!$data) return null;

    if ($beratBadan < $data['median']) {
        // lebih kecil dari median → median − (−1 SD)
        $zscore = ($beratBadan - $data['median']) / ($data['median'] - $data['sd1neg']);
    } else {   
        // lebih besar dari median → (+1 SD) − median     
        $zscore = ($beratBadan - $data['median']) / ($data['sd1pos'] - $data['median']);
    }

    return round($zscore, 2);
}

/**
 * Hitung Z-Score PB/U atau TB/U
 * PB/U digunakan untuk anak 0–24 bulan, TB/U untuk anak >24 bulan.
 * Di sistem ini nilai panjang/tinggi disimpan dalam field tinggi_badan.
 */
function hitungZScoreTBU($tinggiBadan, $umurBulan, $jenisKelamin, $db)
{
    $jk = $jenisKelamin === 'Laki-laki' ? 'L' : 'P';

    $data = $db->table('who_tb_u')
        ->where('jenis_kelamin', $jk)
        ->where('umur_bulan', $umurBulan)
        ->get()
        ->getRowArray();

    if (!$data) return null;

    if ($tinggiBadan < $data['median']) {
        $zscore = ($tinggiBadan - $data['median']) / ($data['median'] - $data['sd1neg']);
    } else {
        $zscore = ($tinggiBadan - $data['median']) / ($data['sd1pos'] - $data['median']);
    }

    return round($zscore, 2);
}

/**
 * Tentukan Status Gizi BB/U
 */
function statusGiziBBU($zscore)
{
    if ($zscore === null) return '-';
    if ($zscore < -3)      return 'BB Sangat Kurang';
    if ($zscore < -2)      return 'BB Kurang';
    if ($zscore <= 1)      return 'BB Normal';
    return 'Risiko BB Lebih';
}

/**
 * Tentukan Status Pertumbuhan TB/U
 */
function statusPertumbuhanTBU($zscore)
{
    if ($zscore === null) return '-';
    if ($zscore < -3)      return 'Sangat Pendek';
    if ($zscore < -2)      return 'Pendek';
    if ($zscore <= 2)      return 'Normal';
    return 'Tinggi';
}

/**
 * Hitung Z-Score BB/TB (Berat Badan per Tinggi Badan) - indikator wasting
 */
function hitungZScoreBBTB($beratBadan, $tinggiBadan, $jenisKelamin, $db)
{
    $jk = $jenisKelamin === 'Laki-laki' ? 'L' : 'P';

    // Cari baris referensi WHO dengan tinggi badan terdekat (data per 0.1 cm)
    $tinggi = (float) $tinggiBadan;
    $data = $db->table('who_bb_tb')
        ->where('jenis_kelamin', $jk)
        ->orderBy('ABS(tinggi - ' . $tinggi . ')', '', false)
        ->limit(1)
        ->get()
        ->getRowArray();

    if (!$data) return null;

    if ($beratBadan < $data['median']) {
        $zscore = ($beratBadan - $data['median']) / ($data['median'] - $data['sd1neg']);
    } else {
        $zscore = ($beratBadan - $data['median']) / ($data['sd1pos'] - $data['median']);
    }

    return round($zscore, 2);
}

/**
 * Tentukan Status Gizi BB/TB (WHO berat-per-tinggi / wasting)
 */
function statusGiziBBTB($zscore)
{
    if ($zscore === null) return '-';
    if ($zscore < -3)      return 'Gizi Buruk';
    if ($zscore < -2)      return 'Gizi Kurang';
    if ($zscore <= 1)      return 'Gizi Baik';
    if ($zscore <= 2)      return 'Berisiko Gizi Lebih';
    if ($zscore <= 3)      return 'Gizi Lebih';
    return 'Obesitas';
}

/**
 * Hitung semua Z-Score sekaligus
 */
function hitungZScore($beratBadan, $tinggiBadan, $tanggalLahir, $tanggalUkur, $jenisKelamin, $db)
{
    $umurBulan = hitungUmurBulan($tanggalLahir, $tanggalUkur);

    // Batasi umur maksimal 60 bulan
    if ($umurBulan > 60) $umurBulan = 60;
    if ($umurBulan < 0)  $umurBulan = 0;

    $zBBU  = hitungZScoreBBU($beratBadan, $umurBulan, $jenisKelamin, $db);
    $zTBU  = hitungZScoreTBU($tinggiBadan, $umurBulan, $jenisKelamin, $db);
    $zBBTB = hitungZScoreBBTB($beratBadan, $tinggiBadan, $jenisKelamin, $db);

    return [
        'umur_bulan'       => $umurBulan,
        'z_score_bb_u'     => $zBBU,
        'z_score_tb_u'     => $zTBU,
        'z_score_bb_tb'    => $zBBTB,
        'status_gizi'      => statusGiziBBU($zBBU),
        'status_tb'        => statusPertumbuhanTBU($zTBU),
        'status_gizi_bbtb' => statusGiziBBTB($zBBTB),
    ];
}