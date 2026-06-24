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

class SchoolScreeningExcelExporter
{
    public function __construct(private readonly SchoolScreeningReportData $reportData) {}

    public function make(School $school): string
    {
        $report = $this->reportData->report($school);
        $path = tempnam(sys_get_temp_dir(), 'mentari-screening-');
        $options = new Options;
        $options->mergeCells(0, 1, 3, 1, 0);
        $options->mergeCells(0, 1, 12, 1, 1);
        $writer = new Writer($options);
        $writer->openToFile($path);
        $writer->setCreator('MENTARI');

        $this->writeSummarySheet($writer, $report);
        $this->writeDataSheet($writer, $report);

        $writer->close();

        return $path;
    }

    private function writeSummarySheet(Writer $writer, array $report): void
    {
        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Ringkasan');
        $sheet->setSheetView((new SheetView)->setShowGridLines(false)->setFreezeRow(6));
        $sheet->setColumnWidth(27, 1);
        $sheet->setColumnWidth(18, 2, 3, 4);

        $titleStyle = $this->style('EC4899', Color::WHITE, 16, true);
        $sectionStyle = $this->style('1F2937', Color::WHITE, 11, true);
        $headerStyle = $this->style('FCE7F3', '831843', 10, true);
        $labelStyle = $this->style('F9FAFB', '374151', 10, true);
        $bodyStyle = $this->style(null, '374151', 10);

        $writer->addRow(Row::fromValues(['LAPORAN HASIL SCREENING SEKOLAH', null, null, null], $titleStyle)->setHeight(28));
        $writer->addRow(Row::fromValues(['Sekolah', $report['school']->name, null, null], $bodyStyle));
        $writer->addRow(Row::fromValues(['Kode sekolah', $report['school']->code ?: '-', null, null], $bodyStyle));
        $writer->addRow(Row::fromValues(['Dibuat', $report['generated_at']->format('Y-m-d H:i'), null, null], $bodyStyle));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(Row::fromValues(['INDIKATOR UTAMA', null, null, null], $sectionStyle));
        $writer->addRow(Row::fromValues(['Indikator', 'Nilai', 'Keterangan', null], $headerStyle));
        $summary = $report['summary'];
        $summaryRows = [
            ['Total siswa', $summary['student_count'], 'Siswa terdaftar'],
            ['Total screening', $summary['screening_count'], $summary['monthly_screenings'].' screening bulan ini'],
            ['Siswa pernah screening', $summary['screened_students'], 'Siswa unik'],
            ['Cakupan screening', $summary['coverage'] / 100, 'Persentase siswa pernah screening'],
            ['Alert aktif', $summary['active_alerts'], $summary['urgent_alerts'].' alert urgent'],
        ];

        foreach ($summaryRows as $index => $values) {
            $styles = [
                0 => $labelStyle,
                1 => $index === 3 ? (clone $bodyStyle)->setFormat('0%') : $bodyStyle,
                2 => $bodyStyle,
            ];
            $writer->addRow(Row::fromValuesWithStyles($values, null, $styles));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['DISTRIBUSI TINGKAT KEPARAHAN', null, null, null], $sectionStyle));
        $writer->addRow(Row::fromValues(['Kategori', 'Depresi', 'Kecemasan', 'Stres'], $headerStyle));

        foreach ($report['distribution']['labels'] as $index => $label) {
            $writer->addRow(Row::fromValues([
                $label,
                $report['distribution']['depression'][$index],
                $report['distribution']['anxiety'][$index],
                $report['distribution']['stress'][$index],
            ], $bodyStyle));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['TREN RATA-RATA SKOR 6 BULAN', null, null, null], $sectionStyle));
        $writer->addRow(Row::fromValues(['Bulan', 'Depresi', 'Kecemasan', 'Stres'], $headerStyle));

        foreach ($report['trend']['labels'] as $index => $label) {
            $writer->addRow(Row::fromValues([
                $label,
                $report['trend']['depression'][$index],
                $report['trend']['anxiety'][$index],
                $report['trend']['stress'][$index],
            ], $bodyStyle));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(
            ['Catatan', 'Hasil screening awal, bukan diagnosis klinis.', null, null],
            (clone $bodyStyle)->setFontItalic()->setShouldWrapText(),
        ));
    }

    private function writeDataSheet(Writer $writer, array $report): void
    {
        $sheet = $writer->addNewSheetAndMakeItCurrent();
        $sheet->setName('Data Screening');
        $sheet->setSheetView((new SheetView)->setShowGridLines(false)->setFreezeRow(6));
        $sheet->setColumnWidth(7, 1);
        $sheet->setColumnWidth(24, 2);
        $sheet->setColumnWidth(28, 3);
        $sheet->setColumnWidth(10, 4);
        $sheet->setColumnWidth(19, 5);
        $sheet->setColumnWidth(12, 6, 8, 10);
        $sheet->setColumnWidth(17, 7, 9, 11);
        $sheet->setColumnWidth(50, 12);
        $sheet->setColumnWidth(12, 13);

        $titleStyle = $this->style('EC4899', Color::WHITE, 16, true);
        $headerStyle = $this->style('1F2937', Color::WHITE, 9, true);
        $bodyStyle = $this->style(null, '374151', 9);
        $wrapStyle = (clone $bodyStyle)->setShouldWrapText();
        $dateStyle = (clone $bodyStyle)->setFormat('yyyy-mm-dd hh:mm');
        $centerStyle = (clone $bodyStyle)->setCellAlignment(CellAlignment::CENTER);

        $writer->addRow(Row::fromValues(['DATA LENGKAP HASIL SCREENING - '.$report['school']->name], $titleStyle)->setHeight(28));
        $writer->addRow(Row::fromValues(['Kode sekolah', $report['school']->code ?: '-'], $bodyStyle));
        $writer->addRow(Row::fromValues(['Dibuat', $report['generated_at']->format('Y-m-d H:i')], $bodyStyle));
        $writer->addRow(Row::fromValues(['Jumlah data', $report['results']->count()], $bodyStyle));
        $writer->addRow(Row::fromValues([]));

        $headers = [
            'No',
            'Nama siswa',
            'Email',
            'Kelas',
            'Waktu screening',
            'Skor depresi',
            'Tingkat depresi',
            'Skor kecemasan',
            'Tingkat kecemasan',
            'Skor stres',
            'Tingkat stres',
            'Ringkasan',
            'ID screening',
        ];
        $writer->addRow(Row::fromValues($headers, $headerStyle)->setHeight(24));

        foreach ($report['results'] as $index => $result) {
            $writer->addRow(Row::fromValuesWithStyles([
                $index + 1,
                $result->user?->name ?? '-',
                $result->user?->email ?? '-',
                $result->user?->level ?? '-',
                $result->taken_at,
                $result->depression_score,
                SchoolScreeningReportData::severityLabel($result->depression_severity),
                $result->anxiety_score,
                SchoolScreeningReportData::severityLabel($result->anxiety_severity),
                $result->stress_score,
                SchoolScreeningReportData::severityLabel($result->stress_severity),
                $result->summary ?: '-',
                $result->id,
            ], null, [
                0 => $centerStyle,
                1 => $bodyStyle,
                2 => $bodyStyle,
                3 => $centerStyle,
                4 => $dateStyle,
                5 => $centerStyle,
                6 => $bodyStyle,
                7 => $centerStyle,
                8 => $bodyStyle,
                9 => $centerStyle,
                10 => $bodyStyle,
                11 => $wrapStyle,
                12 => $centerStyle,
            ])->setHeight(22));
        }

        $lastRow = max(6, 6 + $report['results']->count());
        $sheet->setAutoFilter(new AutoFilter(0, 6, 12, $lastRow));
        $sheet->setPrintTitleRows('1:6');
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
