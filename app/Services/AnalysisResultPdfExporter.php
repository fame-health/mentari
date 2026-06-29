<?php

namespace App\Services;

use App\Models\School;
use App\Support\Pdf\SimplePdfDocument;

class AnalysisResultPdfExporter
{
    private const PAGE_WIDTH = 841.89;

    private const PAGE_HEIGHT = 595.28;

    public function __construct(private readonly AnalysisResultReportData $reportData) {}

    public function make(School $school, ?int $classroomId = null): string
    {
        $report = $this->reportData->report($school, $classroomId);
        $pdf = new SimplePdfDocument;

        $this->drawOverviewPage($pdf, $report);
        $this->drawStudentsTable($pdf, $report);

        return $pdf->output();
    }

    private function drawOverviewPage(SimplePdfDocument $pdf, array $report): void
    {
        $pdf->addPage();
        $this->drawPageHeader($pdf, $report['school'], 'Laporan Hasil Analisis Data');
        $pdf->text(32, 60, 'Dibuat: '.$report['generated_at']->translatedFormat('d F Y, H:i').' WIB', 8.5, false, [100, 107, 118]);
        $pdf->text(32, 72, 'Cakupan kelas: '.$this->classroomLabel($report), 8.5, false, [100, 107, 118]);

        $summary = $report['summary'];
        $cards = [
            ['Total siswa', $summary['student_count'], [236, 72, 153]],
            ['Rata-rata mood', $summary['mood_average'] === null ? '-' : number_format($summary['mood_average'], 1).'/5', [16, 185, 129]],
            ['Cakupan screening', $summary['screening_coverage'].'%', [14, 165, 233]],
            ['Alert aktif', $summary['active_alerts'].' ('.$summary['urgent_alerts'].' urgent)', [245, 158, 11]],
        ];
        $cardWidth = 184;

        foreach ($cards as $index => [$label, $value, $color]) {
            $x = 32 + ($index * 194);
            $pdf->rectangle($x, 84, $cardWidth, 58, [249, 250, 251], [226, 232, 240]);
            $pdf->rectangle($x, 84, 5, 58, $color);
            $pdf->text($x + 16, 105, (string) $label, 8.5, false, [100, 107, 118]);
            $pdf->text($x + 16, 128, (string) $value, 16, true, [31, 41, 55]);
        }

        $pdf->text(32, 175, 'Gabungan Analisis Mood dan Tes Screening', 12.5, true, [31, 41, 55]);
        $pdf->text(32, 191, 'Indikator utama pada scope yang dipilih.', 8.5, false, [100, 107, 118]);
        $this->drawBarList($pdf, $report['combined_analysis']->all(), 42, 210, 340);

        $pdf->text(445, 175, 'Grafik Per Sekolah', 12.5, true, [31, 41, 55]);
        $pdf->text(445, 191, $report['school_overview']['name'].' - ringkasan semua kelas.', 8.5, false, [100, 107, 118]);
        $this->drawBarList($pdf, $report['school_overview']['bars'], 455, 210, 330);

        $pdf->text(32, 385, 'Distribusi Tes Screening DASS-21', 12.5, true, [31, 41, 55]);
        $pdf->text(32, 401, 'Jumlah depresi, kecemasan, dan stres pada setiap kategori.', 8.5, false, [100, 107, 118]);
        $this->drawSeverityDistribution($pdf, $report['severity_distribution']->all(), 42, 420, 755);

        $pdf->text(32, 548, 'Catatan: Laporan analisis ini membantu monitoring sekolah dan kelas, bukan diagnosis klinis.', 8, false, [75, 85, 99]);
        $this->drawFooter($pdf);
    }

    private function drawBarList(SimplePdfDocument $pdf, array $items, float $x, float $top, float $width): void
    {
        foreach (array_values($items) as $index => $item) {
            $rowTop = $top + ($index * 28);
            $label = (string) ($item['label'] ?? '-');
            $value = (string) ($item['value'] ?? '-');
            $percent = (int) ($item['percent'] ?? 0);
            $color = $this->hexColor((string) ($item['color'] ?? '#0ea5e9'));

            $pdf->text($x, $rowTop, $this->limit($label, 34), 8, true, [55, 65, 81]);
            $pdf->text($x + $width - 70, $rowTop, $value, 8, true, [100, 107, 118]);
            $pdf->rectangle($x, $rowTop + 8, $width, 8, [226, 232, 240]);
            $pdf->rectangle($x, $rowTop + 8, max(0, min($width, ($width * $percent) / 100)), 8, $color);
        }
    }

    private function drawSeverityDistribution(SimplePdfDocument $pdf, array $rows, float $x, float $top, float $width): void
    {
        $maxValue = max(1, collect($rows)
            ->flatMap(fn (array $row): array => [$row['depression'], $row['anxiety'], $row['stress']])
            ->max() ?? 1);
        $columns = [
            ['key' => 'depression', 'label' => 'Depresi', 'color' => [244, 63, 94]],
            ['key' => 'anxiety', 'label' => 'Cemas', 'color' => [245, 158, 11]],
            ['key' => 'stress', 'label' => 'Stres', 'color' => [14, 165, 233]],
        ];

        foreach (array_values($rows) as $rowIndex => $row) {
            $rowTop = $top + ($rowIndex * 24);
            $pdf->text($x, $rowTop + 7, (string) $row['label'], 7.5, true, [75, 85, 99]);

            foreach ($columns as $columnIndex => $column) {
                $barX = $x + 112 + ($columnIndex * (($width - 112) / 3));
                $barWidth = (($width - 132) / 3);
                $value = (int) ($row[$column['key']] ?? 0);

                $pdf->text($barX, $rowTop, $column['label'].': '.$value, 7, false, [100, 107, 118]);
                $pdf->rectangle($barX, $rowTop + 8, $barWidth, 6, [226, 232, 240]);
                $pdf->rectangle($barX, $rowTop + 8, ($barWidth * $value) / $maxValue, 6, $column['color']);
            }
        }
    }

    private function drawStudentsTable(SimplePdfDocument $pdf, array $report): void
    {
        $students = $report['students'];
        $columnWidths = [24, 125, 62, 48, 58, 70, 70, 320];
        $headers = ['No', 'Siswa', 'Kelas', 'Streak', 'Mood', 'Screening', 'Risiko', 'Catatan'];
        $pageBottom = 552;
        $currentTop = 0;

        $startPage = function () use ($pdf, $report, $columnWidths, $headers, &$currentTop): void {
            $pdf->addPage();
            $this->drawPageHeader($pdf, $report['school'], 'Data Detail Analisis Siswa');
            $pdf->text(32, 60, 'Cakupan kelas: '.$this->classroomLabel($report), 8.5, false, [100, 107, 118]);
            $currentTop = 78;
            $this->drawTableRow($pdf, $currentTop, $columnWidths, $headers, 24, true);
            $currentTop += 24;
        };

        $startPage();

        if ($students->isEmpty()) {
            $pdf->text(42, $currentTop + 28, 'Belum ada data siswa untuk scope ini.', 10, false, [75, 85, 99]);
            $this->drawFooter($pdf);

            return;
        }

        foreach ($students as $index => $student) {
            $values = [
                (string) ($index + 1),
                $student['name']."\n".$student['email'],
                $student['classroom'],
                $student['streak_days'].' hari',
                $student['mood_average'] === null ? '-' : number_format($student['mood_average'], 1).'/5',
                $student['screening_total'] === null ? '-' : $student['screening_total'].' poin',
                $student['severity_label'],
                'Mood: '.$student['latest_mood'].'; Screening: '.$student['latest_screening_at'].'; Alert aktif: '.$student['active_alerts'],
            ];
            $lineCounts = [];

            foreach ($values as $column => $value) {
                $lineCounts[] = count($this->wrapText((string) $value, $columnWidths[$column] - 8, 6.5));
            }

            $rowHeight = max(25, (max($lineCounts) * 8) + 8);

            if (($currentTop + $rowHeight) > $pageBottom) {
                $this->drawFooter($pdf);
                $startPage();
            }

            $this->drawTableRow($pdf, $currentTop, $columnWidths, $values, $rowHeight);
            $currentTop += $rowHeight;
        }

        $this->drawFooter($pdf);
    }

    private function drawTableRow(
        SimplePdfDocument $pdf,
        float $top,
        array $widths,
        array $values,
        float $height,
        bool $header = false,
    ): void {
        $x = 32;

        foreach ($widths as $index => $width) {
            $pdf->rectangle(
                $x,
                $top,
                $width,
                $height,
                $header ? [31, 41, 55] : (($index % 2 === 0) ? [249, 250, 251] : [255, 255, 255]),
                [209, 213, 219],
                0.35,
            );
            $lines = $this->wrapText((string) ($values[$index] ?? ''), $width - 8, $header ? 7 : 6.5);

            foreach ($lines as $lineIndex => $line) {
                $pdf->text(
                    $x + 4,
                    $top + 14 + ($lineIndex * 8),
                    $line,
                    $header ? 7 : 6.5,
                    $header,
                    $header ? [255, 255, 255] : [55, 65, 81],
                );
            }

            $x += $width;
        }
    }

    private function wrapText(string $text, float $width, float $fontSize): array
    {
        $maxCharacters = max(5, (int) floor($width / ($fontSize * 0.48)));
        $wrapped = wordwrap(preg_replace('/\s+/', ' ', trim($text)) ?: '-', $maxCharacters, "\n", true);

        return explode("\n", $wrapped);
    }

    private function drawPageHeader(SimplePdfDocument $pdf, School $school, string $title): void
    {
        $pdf->rectangle(0, 0, self::PAGE_WIDTH, 10, [236, 72, 153]);
        $pdf->text(32, 35, 'MENTARI', 15, true, [236, 72, 153]);
        $pdf->text(115, 35, $title, 15, true, [31, 41, 55]);
        $pdf->text(32, 53, $school->name.' ('.($school->code ?: 'tanpa kode').')', 9, false, [75, 85, 99]);
    }

    private function classroomLabel(array $report): string
    {
        return $report['classroom'] ? 'Kelas '.$report['classroom']->name : 'Semua kelas';
    }

    private function drawFooter(SimplePdfDocument $pdf): void
    {
        $pdf->line(32, 565, self::PAGE_WIDTH - 32, 565, [226, 232, 240]);
        $pdf->text(32, 580, 'MENTARI - Data bersifat rahasia dan hanya untuk pihak berwenang.', 7.5, false, [107, 114, 128]);
        $pdf->text(self::PAGE_WIDTH - 90, 580, 'Halaman '.$pdf->pageNumber(), 7.5, false, [107, 114, 128]);
    }

    private function hexColor(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return [14, 165, 233];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private function limit(string $value, int $length): string
    {
        return strlen($value) > $length ? substr($value, 0, $length - 3).'...' : $value;
    }
}
