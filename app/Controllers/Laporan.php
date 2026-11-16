<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\PengukuranModel;

class Laporan extends BaseController
{
    protected $anakModel;
    protected $pengukuranModel;
    protected $db;

    protected $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober', '11' => 'November',  '12' => 'Desember'
    ];

    public function __construct()
    {
        $this->anakModel       = new AnakModel();
        $this->pengukuranModel = new PengukuranModel();
        $this->db              = \Config\Database::connect();
    }

    /**
     * Ambil & rekap seluruh data laporan untuk satu bulan.
     * Rekap antropometri dihitung dari z-score dan memakai pengukuran TERAKHIR
     * tiap anak (dedupe), agar konsisten dengan perhitungan di dashboard.
     */
    private function buildLaporan($bulan, $tahun)
    {
        $pengukuran = $this->db->query("
            SELECT p.*, a.nama, a.nik_anak, a.jenis_kelamin, a.tanggal_lahir
            FROM pengukuran p
            JOIN anak a ON a.id = p.anak_id
            WHERE MONTH(p.tanggal_ukur) = ? AND YEAR(p.tanggal_ukur) = ?
            ORDER BY p.tanggal_ukur ASC, a.nama ASC
        ", [$bulan, $tahun])->getResultArray();

        // Pengukuran terakhir tiap anak pada bulan ini (urutan ASC => baris terakhir = paling baru)
        $latest = [];
        foreach ($pengukuran as $p) {
            $latest[$p['anak_id']] = $p;
        }

        // Rekap 3 indikator antropometri
        $rekapGizi = ['BB Normal' => 0, 'BB Kurang' => 0, 'BB Sangat Kurang' => 0, 'Risiko BB Lebih' => 0];
        $rekapTBU  = ['Normal' => 0, 'Pendek' => 0, 'Sangat Pendek' => 0, 'Tinggi' => 0];
        $rekapBBTB = ['Gizi Baik' => 0, 'Berisiko Gizi Lebih' => 0, 'Gizi Lebih' => 0, 'Obesitas' => 0, 'Gizi Kurang' => 0, 'Gizi Buruk' => 0];
        $anakBermasalah = 0;

        foreach ($latest as $p) {
            $bermasalah = false;

            // BB/U
            $zbb = $p['z_score_bb_u'];
            if ($zbb !== null && $zbb !== '') {
                $zbb = (float) $zbb;
                if      ($zbb < -3) { $rekapGizi['BB Sangat Kurang']++; $bermasalah = true; }
                elseif  ($zbb < -2) { $rekapGizi['BB Kurang']++;        $bermasalah = true; }
                elseif  ($zbb <= 1) { $rekapGizi['BB Normal']++; }
                else                { $rekapGizi['Risiko BB Lebih']++; }
            }

            // PB/U atau TB/U (stunting)
            $ztb = $p['z_score_tb_u'];
            if ($ztb !== null && $ztb !== '') {
                $ztb = (float) $ztb;
                if      ($ztb < -3) { $rekapTBU['Sangat Pendek']++; $bermasalah = true; }
                elseif  ($ztb < -2) { $rekapTBU['Pendek']++;        $bermasalah = true; }
                elseif  ($ztb <= 2) { $rekapTBU['Normal']++; }
                else                { $rekapTBU['Tinggi']++; }
            }

            // BB/TB (wasting)
            $zbt = $p['z_score_bb_tb'];
            if ($zbt !== null && $zbt !== '') {
                $zbt = (float) $zbt;
                if      ($zbt < -3) { $rekapBBTB['Gizi Buruk']++;          $bermasalah = true; }
                elseif  ($zbt < -2) { $rekapBBTB['Gizi Kurang']++;         $bermasalah = true; }
                elseif  ($zbt <= 1) { $rekapBBTB['Gizi Baik']++; }
                elseif  ($zbt <= 2) { $rekapBBTB['Berisiko Gizi Lebih']++; }
                elseif  ($zbt <= 3) { $rekapBBTB['Gizi Lebih']++; }
                else                { $rekapBBTB['Obesitas']++; }
            }

            if ($bermasalah) $anakBermasalah++;
        }

        // Rekap kehadiran/penimbangan N/T/O/B (semua baris)
        $rekapNTOB = ['N' => 0, 'T' => 0, 'O' => 0, 'B' => 0, '-' => 0];
        foreach ($pengukuran as $p) {
            $ntob = $p['ntob'] ?? '-';
            if (isset($rekapNTOB[$ntob])) {
                $rekapNTOB[$ntob]++;
            } else {
                $rekapNTOB['-']++;
            }
        }

        // Cakupan partisipasi D/S
        $totalDitimbang = count($latest); // D = jumlah balita yang ditimbang bulan ini
        $totalBalita    = $this->anakModel->where('status_keaktifan', 'Aktif')->countAllResults(); // S
        $cakupanDS      = $totalBalita > 0 ? round($totalDitimbang / $totalBalita * 100, 1) : 0;

        // Kandidat lulus (anak aktif >= 60 bulan)
        $kandidatLulus = $this->db->query("
            SELECT COUNT(*) as total FROM anak
            WHERE status_keaktifan = 'Aktif'
            AND TIMESTAMPDIFF(MONTH, tanggal_lahir, CURDATE()) >= 60
        ")->getRow()->total;

        $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        return [
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'namaBulan' => $this->namaBulan[$bulanKey] . ' ' . $tahun,
            'namaBulanArr'   => $this->namaBulan,
            'pengukuran'     => $pengukuran,
            'rekapGizi'      => $rekapGizi,
            'rekapTBU'       => $rekapTBU,
            'rekapBBTB'      => $rekapBBTB,
            'rekapNTOB'      => $rekapNTOB,
            'totalDitimbang' => $totalDitimbang,
            'totalBalita'    => $totalBalita,
            'cakupanDS'      => $cakupanDS,
            'kandidatLulus'  => $kandidatLulus,
            'anakBermasalah' => $anakBermasalah,
        ];
    }

    public function index()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $data = $this->buildLaporan($bulan, $tahun);
        $data['title'] = 'Laporan Bulanan';

        return view('laporan/index', $data);
    }

    // Export PDF
    public function exportPdf()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $data = $this->buildLaporan($bulan, $tahun);

        $dompdf = new \Dompdf\Dompdf();
        $html   = view('laporan/pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('laporan-' . $this->namaBulan[$bulanKey] . '-' . $tahun . '.pdf', ['Attachment' => true]);
    }

    // Export Excel
    public function exportExcel()
    {
        $periode = $this->getPeriodeFromRequest();
        $bulan   = $periode['bulan'];
        $tahun   = $periode['tahun'];

        $bulanKey = str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);

        $namaBulan = $this->namaBulan;

        $pengukuran = $this->db->query("
            SELECT p.*, a.nama, a.nik_anak, a.jenis_kelamin, a.tanggal_lahir
            FROM pengukuran p
            JOIN anak a ON a.id = p.anak_id
            WHERE MONTH(p.tanggal_ukur) = ? AND YEAR(p.tanggal_ukur) = ?
            ORDER BY p.tanggal_ukur ASC, a.nama ASC
        ", [$bulan, $tahun])->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Bulanan');

        // Judul
        $sheet->setCellValue('A1', 'LAPORAN BULANAN POSYANDU NUSA INDAH');
        $sheet->setCellValue('A2', 'Bulan: ' . $namaBulan[$bulanKey] . ' ' . $tahun);
        $sheet->setCellValue('A3', 'Dicetak: ' . date('d-m-Y H:i'));
        $sheet->mergeCells('A1:N1');
        $sheet->mergeCells('A2:N2');
        $sheet->mergeCells('A3:N3');

        // Style judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Header tabel
        $headers = [
            'No', 'Tgl Ukur', 'NIK', 'Nama', 'JK', 'Umur (bln)',
            'BB (kg)', 'PB atau TB (cm)', 'LK (cm)', 'LiLA (cm)',
            'N/T/O/B', 'Z BB/U', 'Z PB/U atau TB/U', 'Status Gizi'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $sheet->getStyle($col . '5')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 6;
        $no  = 1;
        foreach ($pengukuran as $p) {
            $tglLahir  = new \DateTime($p['tanggal_lahir']);
            $tglUkur   = new \DateTime($p['tanggal_ukur']);
            $umur      = $tglLahir->diff($tglUkur);
            $umurBulan = ($umur->y * 12) + $umur->m;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($p['tanggal_ukur'])));
            $sheet->setCellValue('C' . $row, $p['nik_anak']);
            $sheet->setCellValue('D' . $row, $p['nama']);
            $sheet->setCellValue('E' . $row, $p['jenis_kelamin'] === 'Perempuan' ? 'P' : 'L');
            $sheet->setCellValue('F' . $row, $umurBulan);
            $sheet->setCellValue('G' . $row, $p['berat_badan']);
            $sheet->setCellValue('H' . $row, $p['tinggi_badan']);
            $sheet->setCellValue('I' . $row, $p['lingkar_kepala'] ?? '-');
            $sheet->setCellValue('J' . $row, $p['lila'] ?? '-');
            $sheet->setCellValue('K' . $row, $p['ntob'] ?? '-');
            $sheet->setCellValue('L' . $row, $p['z_score_bb_u'] ?? '-');
            $sheet->setCellValue('M' . $row, $p['z_score_tb_u'] ?? '-');
            $sheet->setCellValue('N' . $row, $p['status_gizi'] ?? '-');
            $row++;
        }

        // Auto size
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan-' . $namaBulan[$bulanKey] . '-' . $tahun . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getPeriodeFromRequest(): array
    {
        $bulan = (int) ($this->request->getGet('bulan') ?? date('m'));
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) date('m');
        }

        $tahunSekarang = (int) date('Y');

        if ($tahun < 2020 || $tahun > ($tahunSekarang + 1)) {
            $tahun = $tahunSekarang;
        }

        return [
            'bulan' => $bulan,
            'tahun' => $tahun,
        ];
    }
}
