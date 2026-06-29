<?php

namespace App\Services;

use App\Models\School;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\AutoFilter;
use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

class AnalysisResultExcelExporter
{
    public function __construct(private readonly AnalysisResultReportData $reportData) {}

    public function make(School $school, ?int $classroomId = null): string
    {
        $report = $this->reportData->report($school, $classroomId);
        $path = tempnam(sys_get_temp_dir(), 'mentari-analysis-');
        $writer = new Writer(new Options);
        $writer->openToFile($path);
        $writer->setCreator('MENTARI');

        $this->writeSummarySheet($writer, $report);
        $this->writeStudentsSheet($writer, $report);
        $this->writeClassroomsSheet($writer, $report);

        $writer->close();

        return $path;
    }

    private function writeSummarySheet(Writer $writer, array $report): void
    {
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Ringkasan Analisis');
        $sheet->setSheetView((new SheetView)->setShowGridLines(false)->setFreezeRow(7));
        $sheet->setColumnWidth(28, 1);
        $sheet->setColumnWidth(18, 2, 3, 4);

        $titleStyle = $this->style('EC4899', Color::WHITE, 16, true);
        $sectionStyle = $this->style('1F2937', Color::WHITE, 11, true);
        $headerStyle = $this->style('FCE7F3', '831843', 10, true);
        $labelStyle = $this->style('F9FAFB', '374151', 10, true);
        $bodyStyle = $this->style(null, '374151', 10);
        $percentStyle = (clone $bodyStyle)->setFormat('0%');

        $writer->addRow(Row::fromValues(['LAPORAN HASIL ANALISIS DATA'], $titleStyle)->setHeight(28));
        $writer->addRow(Row::fromValues(['Sekolah', $report['school']->name], $bodyStyle));
        $writer->addRow(Row::fromValues(['Kode sekolah', $report['school']->code ?: '-'], $bodyStyle));
        $writer->addRow(Row::fromValues(['Kelas', $this->classroomLabel($report)], $bodyStyle));
        $writer->addRow(Row::fromValues(['Dibuat', $report['generated_at']->format('Y-m-d H:i')], $bodyStyle));
        $writer->addRow(Row::fromValues([]));

        $summary = $report['summary'];
        $writer->addRow(Row::fromValues(['INDIKATOR UTAMA'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Indikator', 'Nilai', 'Keterangan'], $headerStyle));

        foreach ([
            ['Total siswa', $summary['student_count'], $summary['active_logins'].' aktif login 7 hari terakhir'],
            ['Full streak login', $summary['max_streak'], 'Rata-rata '.number_format($summary['average_streak'], 1).' hari'],
            ['Mood rata-rata', $summary['mood_average'] === null ? '-' : $summary['mood_average'], $summary['mood_entries'].' check-in dari '.$summary['mood_students'].' siswa'],
            ['Cakupan tes screening', $summary['screening_coverage'] / 100, $summary['screened_students'].' siswa sudah punya hasil screening'],
            ['Alert aktif', $summary['active_alerts'], $summary['urgent_alerts'].' alert urgent'],
        ] as $index => $values) {
            $writer->addRow(Row::fromValuesWithStyles($values, null, [
                0 => $labelStyle,
                1 => $index === 3 ? $percentStyle : $bodyStyle,
                2 => $bodyStyle,
            ]));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['GABUNGAN ANALISIS'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Indikator', 'Nilai', 'Persentase'], $headerStyle));

        foreach ($report['combined_analysis'] as $item) {
            $writer->addRow(Row::fromValuesWithStyles([
                $item['label'],
                $item['value'],
                $item['percent'] / 100,
            ], null, [
                0 => $bodyStyle,
                1 => $bodyStyle,
                2 => $percentStyle,
            ]));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['DISTRIBUSI TES SCREENING DASS-21'], $sectionStyle));
        $writer->addRow(Row::fromValues(['Kategori', 'Depresi', 'Kecemasan', 'Stres'], $headerStyle));

        foreach ($report['severity_distribution'] as $row) {
            $writer->addRow(Row::fromValues([
                $row['label'],
                $row['depression'],
                $row['anxiety'],
                $row['stress'],
            ], $bodyStyle));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(
            ['Catatan', 'Laporan analisis ini membantu monitoring sekolah dan kelas, bukan diagnosis klinis.'],
            (clone $bodyStyle)->setFontItalic()->setShouldWrapText(),
        ));
    }

    private function writeStudentsSheet(Writer $writer, array $report): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('Data Siswa');
        $sheet->setSheetView((new SheetView)->setShowGridLines(false)->setFreezeRow(7));
        $sheet->setColumnWidth(7, 1);
        $sheet->setColumnWidth(24, 2);
        $sheet->setColumnWidth(28, 3);
        $sheet->setColumnWidth(16, 4);
        $sheet->setColumnWidth(12, 5, 7, 8, 10, 11, 13, 16, 17);
        $sheet->setColumnWidth(18, 6, 9, 14, 15);
        $sheet->setColumnWidth(20, 12);

        $titleStyle = $this->style('EC4899', Color::WHITE, 16, true);
        $headerStyle = $this->style('1F2937', Color::WHITE, 9, true);
        $bodyStyle = $this->style(null, '374151', 9);
        $centerStyle = (clone $bodyStyle)->setCellAlignment(CellAlignment::CENTER);

        $writer->addRow(Row::fromValues(['DATA DETAIL SISWA - '.$report['school']->name], $titleStyle)->setHeight(28));
        $writer->addRow(Row::fromValues(['Kode sekolah', $report['school']->code ?: '-'], $bodyStyle));
        $writer->addRow(Row::fromValues(['Kelas', $this->classroomLabel($report)], $bodyStyle));
        $writer->addRow(Row::fromValues(['Dibuat', $report['generated_at']->format('Y-m-d H:i')], $bodyStyle));
        $writer->addRow(Row::fromValues(['Jumlah siswa', $report['students']->count()], $bodyStyle));
        $writer->addRow(Row::fromValues([]));

        $headers = [
            'No',
            'Nama siswa',
            'Email',
            'Kelas',
            'Streak hari',
            'Aktivitas terakhir',
            'Mood rata-rata',
            'Jumlah mood',
            'Mood terbaru',
            'Energi rata-rata',
            'Stres rata-rata',
            'Skor screening',
            'Jumlah screening',
            'Screening terbaru',
            'Risiko',
            'Alert aktif',
            'ID siswa',
        ];
        $writer->addRow(Row::fromValues($headers, $headerStyle)->setHeight(24));

        foreach ($report['students'] as $index => $student) {
            $writer->addRow(Row::fromValuesWithStyles([
                $index + 1,
                $student['name'],
                $student['email'],
                $student['classroom'],
                $student['streak_days'],
                $student['last_activity'],
                $student['mood_average'] ?? '-',
                $student['mood_count'],
                $student['latest_mood'],
                $student['energy_average'] ?? '-',
                $student['stress_average'] ?? '-',
                $student['screening_total'] ?? '-',
                $student['screening_count'],
                $student['latest_screening_at'],
                $student['severity_label'],
                $student['active_alerts'],
                $student['id'],
            ], null, [
                0 => $centerStyle,
                1 => $bodyStyle,
                2 => $bodyStyle,
                3 => $bodyStyle,
                4 => $centerStyle,
                5 => $bodyStyle,
                6 => $centerStyle,
                7 => $centerStyle,
                8 => $bodyStyle,
                9 => $centerStyle,
                10 => $centerStyle,
                11 => $centerStyle,
                12 => $centerStyle,
                13 => $bodyStyle,
                14 => $bodyStyle,
                15 => $centerStyle,
                16 => $centerStyle,
            ])->setHeight(22));
        }

        $lastRow = max(7, 7 + $report['students']->count());
        $sheet->setAutoFilter(new AutoFilter(0, 7, 16, $lastRow));
        $sheet->setPrintTitleRows('1:7');
    }

    private function writeClassroomsSheet(Writer $writer, array $report): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('Data Kelas');
        $sheet->setSheetView((new SheetView)->setShowGridLines(false)->setFreezeRow(6));
        $sheet->setColumnWidth(24, 1);
        $sheet->setColumnWidth(16, 2, 3, 4, 5, 6, 7);

        $titleStyle = $this->style('14B8A6', Color::WHITE, 16, true);
        $headerStyle = $this->style('1F2937', Color::WHITE, 9, true);
        $bodyStyle = $this->style(null, '374151', 9);
        $percentStyle = (clone $bodyStyle)->setFormat('0%');
        $centerStyle = (clone $bodyStyle)->setCellAlignment(CellAlignment::CENTER);

        $writer->addRow(Row::fromValues(['DATA PER KELAS - '.$report['school']->name], $titleStyle)->setHeight(28));
        $writer->addRow(Row::fromValues(['Kelas aktif', $report['classrooms']->count()], $bodyStyle));
        $writer->addRow(Row::fromValues(['Scope export', $this->classroomLabel($report)], $bodyStyle));
        $writer->addRow(Row::fromValues(['Dibuat', $report['generated_at']->format('Y-m-d H:i')], $bodyStyle));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(Row::fromValues([
            'Kelas',
            'Siswa',
            'Screening',
            'Mood entries',
            'Mood rata-rata',
            'Cakupan screening',
            'Rata-rata streak',
        ], $headerStyle)->setHeight(24));

        foreach ($report['classrooms'] as $classroom) {
            $summary = $classroom['summary'];
            $writer->addRow(Row::fromValuesWithStyles([
                $classroom['name'],
                $summary['student_count'],
                $summary['screening_count'],
                $summary['mood_entries'],
                $summary['mood_average'] ?? '-',
                $summary['screening_coverage'] / 100,
                $summary['average_streak'],
            ], null, [
                0 => $bodyStyle,
                1 => $centerStyle,
                2 => $centerStyle,
                3 => $centerStyle,
                4 => $centerStyle,
                5 => $percentStyle,
                6 => $centerStyle,
            ])->setHeight(22));
        }

        $lastRow = max(6, 6 + $report['classrooms']->count());
        $sheet->setAutoFilter(new AutoFilter(0, 6, 6, $lastRow));
        $sheet->setPrintTitleRows('1:6');
    }

    private function classroomLabel(array $report): string
    {
        return $report['classroom'] ? 'Kelas '.$report['classroom']->name : 'Semua kelas';
    }

    private function style(
        ?string $background,
        string $fontColor,
        int $fontSize,
        bool $bold = false,
    ): Style {
        $style = (new Style)
            ->setFontName('Calibri')
            ->setFontSize($fontSize)
            ->setFontColor($fontColor)
            ->setCellVerticalAlignment('center');

        if ($background) {
            $style->setBackgroundColor($background);
        }

        if ($bold) {
            $style->setFontBold();
        }

        return $style;
    }
}
