<?php

namespace App\Filament\Resources\EducationContents\Schemas;

use App\Models\EducationContent;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class EducationContentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi konten')
                    ->description('Ringkasan, status, dan metadata penting dalam satu tampilan padat.')
                    ->icon('heroicon-o-document-text')
                    ->extraAttributes(['class' => 'mentari-content-view-section mentari-content-view-section--summary'])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextEntry::make('summary')
                            ->label('Ringkasan')
                            ->size(TextSize::Medium)
                            ->weight(FontWeight::Medium)
                            ->color('gray')
                            ->placeholder('Ringkasan belum tersedia.')
                            ->columnSpanFull(),
                        TextEntry::make('publication_status')
                            ->label('Status')
                            ->state(fn (EducationContent $record): string => self::publicationStatus($record))
                            ->badge()
                            ->color(fn (EducationContent $record): string => self::publicationColor($record))
                            ->icon(fn (EducationContent $record): string => self::publicationIcon($record)),
                        TextEntry::make('category.title')
                            ->label('Kategori')
                            ->icon('heroicon-o-folder')
                            ->placeholder('Tanpa kategori'),
                        TextEntry::make('type')
                            ->label('Jenis konten')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => self::typeLabel($state))
                            ->color(fn (string $state): string => self::typeColor($state))
                            ->icon(fn (string $state): string => self::typeIcon($state)),
                        TextEntry::make('reading_time')
                            ->label('Estimasi baca')
                            ->state(fn (EducationContent $record): string => self::readingTime($record))
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('published_at')
                            ->label('Waktu publikasi')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-calendar-days')
                            ->placeholder('Belum dijadwalkan'),
                        TextEntry::make('media_url')
                            ->label('Media pendukung')
                            ->formatStateUsing(fn (): string => 'Buka media pendukung')
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->color('primary')
                            ->url(fn (EducationContent $record): ?string => $record->media_url)
                            ->openUrlInNewTab()
                            ->visible(fn (EducationContent $record): bool => filled($record->media_url)),
                        ColorEntry::make('accent_color')
                            ->label('Warna aksen')
                            ->copyable()
                            ->copyMessage('Kode warna disalin')
                            ->visible(fn (EducationContent $record): bool => filled($record->accent_color)),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('-'),
                    ]),
                Section::make('Isi artikel')
                    ->description('Materi utama yang akan dibaca pengguna.')
                    ->icon('heroicon-o-book-open')
                    ->extraAttributes(['class' => 'mentari-content-view-section mentari-content-view-section--article'])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('body')
                            ->hiddenLabel()
                            ->formatStateUsing(fn (?string $state): string => self::formatBody($state))
                            ->html()
                            ->prose()
                            ->placeholder('Isi konten belum tersedia.'),
                    ]),
                Section::make('Riwayat')
                    ->description('Informasi singkat kapan konten dibuat dan diperbarui.')
                    ->icon('heroicon-o-clock')
                    ->extraAttributes(['class' => 'mentari-content-view-section mentari-content-view-section--history'])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('-'),
                    ]),
            ]);
    }

    private static function publicationStatus(EducationContent $record): string
    {
        return match (true) {
            ! $record->is_active => 'Nonaktif',
            $record->published_at === null => 'Belum dipublikasikan',
            $record->published_at->isFuture() => 'Terjadwal',
            default => 'Tayang',
        };
    }

    private static function publicationColor(EducationContent $record): string
    {
        return match (self::publicationStatus($record)) {
            'Tayang' => 'success',
            'Terjadwal' => 'info',
            'Belum dipublikasikan' => 'warning',
            default => 'gray',
        };
    }

    private static function publicationIcon(EducationContent $record): string
    {
        return match (self::publicationStatus($record)) {
            'Tayang' => 'heroicon-o-check-circle',
            'Terjadwal' => 'heroicon-o-clock',
            'Belum dipublikasikan' => 'heroicon-o-exclamation-circle',
            default => 'heroicon-o-pause-circle',
        };
    }

    private static function typeLabel(string $type): string
    {
        return match ($type) {
            'article' => 'Artikel',
            'infographic' => 'Infografis',
            'video' => 'Video',
            default => ucfirst($type),
        };
    }

    private static function typeColor(string $type): string
    {
        return match ($type) {
            'article' => 'info',
            'infographic' => 'warning',
            'video' => 'danger',
            default => 'gray',
        };
    }

    private static function typeIcon(string $type): string
    {
        return match ($type) {
            'article' => 'heroicon-o-document-text',
            'infographic' => 'heroicon-o-photo',
            'video' => 'heroicon-o-play-circle',
            default => 'heroicon-o-document',
        };
    }

    private static function readingTime(EducationContent $record): string
    {
        return $record->read_time_label
            ?: ($record->read_time_minutes ? $record->read_time_minutes.' menit' : 'Belum ditentukan');
    }

    private static function formatBody(?string $body): string
    {
        if (blank($body)) {
            return '';
        }

        if ($body === strip_tags($body)) {
            return nl2br(e($body));
        }

        return $body;
    }
}
