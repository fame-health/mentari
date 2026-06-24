<?php

namespace App\Support\Pdf;

class SimplePdfDocument
{
    private const PAGE_WIDTH = 841.89;

    private const PAGE_HEIGHT = 595.28;

    private array $pages = [];

    private int $currentPage = -1;

    public function addPage(): int
    {
        $this->pages[] = '';
        $this->currentPage = array_key_last($this->pages);

        return $this->currentPage + 1;
    }

    public function pageNumber(): int
    {
        return $this->currentPage + 1;
    }

    public function text(
        float $x,
        float $top,
        string $text,
        float $size = 10,
        bool $bold = false,
        array $color = [35, 39, 47],
    ): void {
        $this->ensurePage();
        $encoded = $this->escapeText($text);
        $font = $bold ? 'F2' : 'F1';
        $y = self::PAGE_HEIGHT - $top;
        [$red, $green, $blue] = $this->pdfColor($color);

        $this->append(sprintf(
            "BT /%s %.2F Tf %.3F %.3F %.3F rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n",
            $font,
            $size,
            $red,
            $green,
            $blue,
            $x,
            $y,
            $encoded,
        ));
    }

    public function rectangle(
        float $x,
        float $top,
        float $width,
        float $height,
        ?array $fill = null,
        ?array $stroke = null,
        float $lineWidth = 0.5,
    ): void {
        $this->ensurePage();
        $commands = sprintf("%.2F w\n", $lineWidth);

        if ($fill) {
            [$red, $green, $blue] = $this->pdfColor($fill);
            $commands .= sprintf("%.3F %.3F %.3F rg\n", $red, $green, $blue);
        }

        if ($stroke) {
            [$red, $green, $blue] = $this->pdfColor($stroke);
            $commands .= sprintf("%.3F %.3F %.3F RG\n", $red, $green, $blue);
        }

        $commands .= sprintf(
            "%.2F %.2F %.2F %.2F re %s\n",
            $x,
            self::PAGE_HEIGHT - $top - $height,
            $width,
            $height,
            $fill && $stroke ? 'B' : ($fill ? 'f' : 'S'),
        );

        $this->append($commands);
    }

    public function line(
        float $x1,
        float $top1,
        float $x2,
        float $top2,
        array $color = [210, 214, 220],
        float $lineWidth = 0.5,
    ): void {
        $this->ensurePage();
        [$red, $green, $blue] = $this->pdfColor($color);

        $this->append(sprintf(
            "%.2F w %.3F %.3F %.3F RG %.2F %.2F m %.2F %.2F l S\n",
            $lineWidth,
            $red,
            $green,
            $blue,
            $x1,
            self::PAGE_HEIGHT - $top1,
            $x2,
            self::PAGE_HEIGHT - $top2,
        ));
    }

    public function output(): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
        ];
        $pageReferences = [];

        foreach ($this->pages as $index => $content) {
            $pageObject = 5 + ($index * 2);
            $contentObject = $pageObject + 1;
            $pageReferences[] = "{$pageObject} 0 R";
            $objects[$pageObject] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::PAGE_WIDTH,
                self::PAGE_HEIGHT,
                $contentObject,
            );
            $objects[$contentObject] = '<< /Length '.strlen($content)." >>\nstream\n{$content}endstream";
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageReferences).'] /Count '.count($this->pages).' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= "{$number} 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $objectCount = max(array_keys($objects));
        $pdf .= "xref\n0 ".($objectCount + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($number = 1; $number <= $objectCount; $number++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$number]);
        }

        $pdf .= "trailer\n<< /Size ".($objectCount + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function ensurePage(): void
    {
        if ($this->currentPage < 0) {
            $this->addPage();
        }
    }

    private function append(string $commands): void
    {
        $this->pages[$this->currentPage] .= $commands;
    }

    private function escapeText(string $text): string
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);

        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\\(', '\\)', '', ' '],
            $encoded === false ? $text : $encoded,
        );
    }

    private function pdfColor(array $color): array
    {
        return [
            ($color[0] ?? 0) / 255,
            ($color[1] ?? 0) / 255,
            ($color[2] ?? 0) / 255,
        ];
    }
}
