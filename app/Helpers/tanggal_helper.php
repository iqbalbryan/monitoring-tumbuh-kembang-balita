<?php

/**
 * Helper tanggal berbahasa Indonesia untuk Posyandu Nusa Indah.
 */

if (!function_exists('tglIndo')) {
    /**
     * Format tanggal menjadi "01 Juli 2026".
     * Jika $withTime true, hasilnya "01 Juli 2026 14:30".
     *
     * @param string|int|null $tanggal  String tanggal, timestamp, atau null (= sekarang)
     * @param bool             $withTime Sertakan jam (H:i)
     */
    function tglIndo($tanggal = null, $withTime = false)
    {
        if ($tanggal === null || $tanggal === '') {
            $ts = time();
        } elseif (is_numeric($tanggal)) {
            $ts = (int) $tanggal;
        } else {
            $ts = strtotime($tanggal);
        }

        if ($ts === false) {
            return (string) $tanggal;
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $hasil = date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

        if ($withTime) {
            $hasil .= ' ' . date('H:i', $ts);
        }

        return $hasil;
    }
}

if (!function_exists('blnTahunIndo')) {
    /**
     * Format menjadi "Juli 2026" (nama bulan + tahun, tanpa tanggal).
     *
     * @param string|int|null $tanggal String tanggal, timestamp, atau null (= sekarang)
     */
    function blnTahunIndo($tanggal = null)
    {
        if ($tanggal === null || $tanggal === '') {
            $ts = time();
        } elseif (is_numeric($tanggal)) {
            $ts = (int) $tanggal;
        } else {
            $ts = strtotime($tanggal);
        }

        if ($ts === false) {
            return (string) $tanggal;
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}

if (!function_exists('blnSingkatIndo')) {
    /**
     * Format menjadi "Agu 2026" (singkatan bulan Indonesia + tahun) untuk label grafik.
     *
     * @param string|int|null $tanggal String tanggal, timestamp, atau null (= sekarang)
     */
    function blnSingkatIndo($tanggal = null)
    {
        if ($tanggal === null || $tanggal === '') {
            $ts = time();
        } elseif (is_numeric($tanggal)) {
            $ts = (int) $tanggal;
        } else {
            $ts = strtotime($tanggal);
        }

        if ($ts === false) {
            return (string) $tanggal;
        }

        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        return $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}
