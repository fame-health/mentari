<?php

namespace App\Services;

use App\Models\School;
use App\Support\Pdf\SimplePdfDocument;

class SchoolScreeningPdfExporter
{
    private const PAGE_WIDTH = 841.89;

    private const PAGE_HEIGHT = 595.28;

    public function __construct(private readonly SchoolScreeningReportData $reportData) {}

    public function make(School $school, ?int $classroomId = null): string
    {
        $report = $this->reportData->report($school, $classroomId);
        $pdf = new SimplePdfDocument;

        $this->drawOverviewPage($pdf, $report);
        $this->drawResultsTable($pdf, $report);

        return $pdf->output();
    }

    private function drawOverviewPage(SimplePdfDocument $pdf, array $report): void
    {
        $pdf->addPage();
        $this->drawPageHeader($pdf, $report['school'], 'Laporan Hasil Screening Sekolah');
        $pdf->text(32, 60, 'Dibuat: '.$report['generated_at']->translatedFormat('d F Y, H:i').' WIB', 8.5, false, [100, 107, 118]);
        $pdf->text(32, 72, 'Cakupan kelas: '.$this->classroomLabel($report), 8.5, false, [100, 107, 118]);

        $summary = $report['summary'];
        $cards = [
            ['Total siswa', $summary['student_count'], [236, 72, 153]],
            ['Total screening', $summary['screening_count'], [14, 165, 233]],
            ['Cakupan screening', $summary['coverage'].'%', [16, 185, 129]],
            ['Alert aktif', $summary['active_alerts'].' ('.$summary['urgent_alerts'].' urgent)', [245, 158, 11]],
        ];
        $cardWidth = 184;

        foreach ($cards as $index => [$label, $value, $color]) {
            $x = 32 + ($index * 194);
            $pdf->rectangle($x, 82, $cardWidth, 58, [249, 250, 251], [226, 232, 240]);
            $pdf->rectangle($x, 82, 5, 58, $color);
            $pdf->text($x + 16, 103, (string) $label, 8.5, false, [100, 107, 118]);
            $pdf->text($x + 16, 126, (string) $value, 16, true, [31, 41, 55]);
        }

        $pdf->text(32, 175, 'Diagram Batang Distribusi Tingkat Keparahan DASS-21', 12.5, true, [31, 41, 55]);
        $pdf->text(32, 191, 'Jumlah hasil screening pada setiap kategori.', 8.5, false, [100, 107, 118]);
        $this->drawSeverityChart($pdf, $report['distribution'], 50, 215, 742, 235);

        $legend = [
            ['Depresi', [244, 63, 94]],
            ['Kecemasan', [245, 158, 11]],
            ['Stres', [14, 165, 233]],
        ];

        foreach ($legend as $index => [$label, $color]) {
            $x = 290 + ($index * 105);
            $pdf->rectangle($x, 470, 10, 10, $color);
            $pdf->text($x + 16, 479, $label, 8.5, false, [55, 65, 81]);
        }

        $pdf->text(32, 515, 'Catatan', 9, true, [31, 41, 55]);
        $pdf->text(
            32,
            530,
            'Laporan ini merupakan hasil screening awal dan bukan diagnosis klinis. Tindak lanjut perlu dilakukan oleh tenaga yang berwenang.',
            8,
            false,
            [75, 85, 99],
        );
        $this->drawFooter($pdf);
    }

    private function drawSeverityChart(
        SimplePdfDocument $pdf,
        array $distribution,
        float $x,
        float $top,
        float $width,
        float $height,
    ): void {
        $series = [
            ['values' => $distribution['depression'], 'color' => [244, 63, 94]],
            ['values' => $distribution['anxiety'], 'color' => [245, 158, 11]],
            ['values' => $distribution['stress'], 'color' => [14, 165, 233]],
        ];
        $maxValue = max(1, ...$distribution['depression'], ...$distribution['anxiety'], ...$distribution['stress']);
        $axisMax = max(5, (int) ceil($maxValue / 5) * 5);
        $plotLeft = $x + 36;
        $plotTop = $top + 10;
        $plotWidth = $width - 48;
        $plotHeight = $height - 42;

        for ($step = 0; $step <= 5; $step++) {
            $value = (int) round($axisMax * $step / 5);
            $lineTop = $plotTop + $plotHeight - ($plotHeight * $step / 5);
            $pdf->line($plotLeft, $lineTop, $plotLeft + $plotWidth, $lineTop, [226, 232, 240]);
            $pdf->text($x, $lineTop + 3, (string) $value, 7, false, [100, 107, 118]);
        }

        $groupWidth = $plotWidth / count($distribution['labels']);
        $barWidth = min(15, ($groupWidth - 18) / 3);

        foreach ($distribution['labels'] as $categoryIndex => $label) {
            $groupX = $plotLeft + ($categoryIndex * $groupWidth);
            $barsWidth = ($barWidth * 3) + 6;
            $barStart = $groupX + (($groupWidth - $barsWidth) / 2);

            foreach ($series as $seriesIndex => $data) {
                $value = $data['values'][$categoryIndex] ?? 0;
                $barHeight = $value > 0 ? max(2, ($value / $axisMax) * $plotHeight) : 0;
                $barX = $barStart + ($seriesIndex * ($barWidth + 3));
                $barTop = $plotTop + $plotHeight - $barHeight;

                if ($barHeight > 0) {
                    $pdf->rectangle($barX, $barTop, $barWidth, $barHeight, $data['color']);
                    $pdf->text($barX + 2, $barTop - 4, (string) $value, 6.5, true, [55, 65, 81]);
                }
            }

            $labelText = $label === 'Sangat Berat' ? 'Sangat berat' : $label;
            $labelX = $groupX + ($groupWidth / 2) - (strlen($labelText) * 2.1);
            $pdf->text($labelX, $plotTop + $plotHeight + 17, $labelText, 7, false, [75, 85, 99]);
        }
    }

    private function drawResultsTable(SimplePdfDocument $pdf, array $report): void
    {
        $results = $report['results'];
        $columnWidths = [25, 120, 45, 82, 92, 92, 92, 230];
        $headers = ['No', 'Siswa', 'Kelas', 'Tanggal', 'Depresi', 'Kecemasan', 'Stres', 'Ringkasan'];
        $pageBottom = 552;
        $currentTop = 0;

        $startPage = function () use ($pdf, $report, $columnWidths, $headers, &$currentTop): void {
            $pdf->addPage();
            $this->drawPageHeader($pdf, $report['school'], 'Data Lengkap Hasil Screening');
            $pdf->text(32, 60, 'Urutan: hasil terbaru terlebih dahulu', 8.5, false, [100, 107, 118]);
            $pdf->text(32, 72, 'Cakupan kelas: '.$this->classroomLabel($report), 8.5, false, [100, 107, 118]);
            $currentTop = 78;
            $this->drawTableRow($pdf, $currentTop, $columnWidths, $headers, 24, true);
            $currentTop += 24;
        };

        $startPage();

        if ($results->isEmpty()) {
            $pdf->text(42, $currentTop + 28, 'Belum ada hasil screening untuk sekolah ini.', 10, false, [75, 85, 99]);
            $this->drawFooter($pdf);

            return;
        }

        foreach ($results as $index => $result) {
            $values = [
                (string) ($index + 1),
                $result->user?->name ?? '-',
                $result->user?->level ?? '-',
                $result->taken_at?->format('d-m-Y H:i') ?? '-',
                $result->depression_score.' / '.SchoolScreeningReportData::severityLabel($result->depression_severity),
                $result->anxiety_score.' / '.SchoolScreeningReportData::severityLabel($result->anxiety_severity),
                $result->stress_score.' / '.SchoolScreeningReportData::severityLabel($result->stress_severity),
                $result->summary ?: '-',
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
}
