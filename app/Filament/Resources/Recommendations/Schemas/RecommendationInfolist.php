<?php

namespace App\Filament\Resources\Recommendations\Schemas;

use App\Models\Recommendation;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class RecommendationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi rekomendasi')
                    ->description('Teks inilah yang akan terlihat oleh siswa di aplikasi.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->extraAttributes(['class' => 'mentari-recommendation-view-section mentari-recommendation-view-section--main'])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->formatStateUsing(fn (?string $state): string => self::formatMultiline($state))
                            ->html()
                            ->prose()
                            ->size(TextSize::Medium)
                            ->weight(FontWeight::Medium)
                            ->color('gray')
                            ->placeholder('Isi rekomendasi belum tersedia.')
                            ->columnSpanFull(),
                        TextEntry::make('category')
                            ->label('Jenis')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::categoryLabel($state))
                            ->color(fn (?string $state): string => self::categoryColor($state))
                            ->icon(fn (?string $state): string => self::categoryIcon($state)),
                        TextEntry::make('severity')
                            ->label('Status DASS-21')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::severityLabel($state))
                            ->color(fn (?string $state): string => self::severityColor($state))
                            ->icon(fn (?string $state): string => self::severityIcon($state))
                            ->visible(fn (Recommendation $record): bool => $record->category === Recommendation::COUNSELING_SCRIPT_CATEGORY || filled($record->severity)),
                        TextEntry::make('display_status')
                            ->label('Status')
                            ->state(fn (Recommendation $record): string => $record->is_active ? 'Aktif' : 'Nonaktif')
                            ->badge()
                            ->color(fn (Recommendation $record): string => $record->is_active ? 'success' : 'gray')
                            ->icon(fn (Recommendation $record): string => $record->is_active ? 'heroicon-o-check-circle' : 'heroicon-o-pause-circle'),
                    ]),
                Section::make('Detail singkat')
                    ->description('Informasi pendukung untuk admin. Dibuka hanya saat dibutuhkan.')
                    ->icon('heroicon-o-information-circle')
                    ->extraAttributes(['class' => 'mentari-recommendation-view-section mentari-recommendation-view-section--meta'])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul internal')
                            ->weight(FontWeight::SemiBold)
                            ->columnSpan([
                                'default' => 1,
                                'sm' => 2,
                            ]),
                        TextEntry::make('duration_display')
                            ->label('Durasi')
                            ->state(fn (Recommendation $record): string => self::durationText($record))
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('priority')
                            ->label('Prioritas')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::priorityLabel($state))
                            ->color(fn (?string $state): string => self::priorityColor($state))
                            ->icon('heroicon-o-flag'),
                        ColorEntry::make('accent_color')
                            ->label('Warna aksen')
                            ->copyable()
                            ->copyMessage('Kode warna disalin')
                            ->visible(fn (Recommendation $record): bool => filled($record->accent_color)),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('-'),
                    ]),
            ]);
    }

    private static function categoryLabel(?string $category): string
    {
        return Recommendation::CATEGORY_LABELS[$category] ?? ($category ?: '-');
    }

    private static function categoryColor(?string $category): string
    {
        return match ($category) {
            Recommendation::COUNSELING_SCRIPT_CATEGORY => 'info',
            'relaxation' => 'success',
            'reflection' => 'warning',
            default => 'gray',
        };
    }

    private static function categoryIcon(?string $category): string
    {
        return match ($category) {
            Recommendation::COUNSELING_SCRIPT_CATEGORY => 'heroicon-o-chat-bubble-left-right',
            'relaxation' => 'heroicon-o-sparkles',
            'reflection' => 'heroicon-o-pencil-square',
            'activity' => 'heroicon-o-bolt',
            default => 'heroicon-o-tag',
        };
    }

    private static function severityLabel(?string $severity): string
    {
        return $severity ? Recommendation::SEVERITY_LABELS[$severity] : '-';
    }

    private static function severityColor(?string $severity): string
    {
        return match ($severity) {
            'normal' => 'success',
            'mild' => 'info',
            'moderate' => 'warning',
            'severe', 'extremely_severe' => 'danger',
            default => 'gray',
        };
    }

    private static function severityIcon(?string $severity): string
    {
        return match ($severity) {
            'normal' => 'heroicon-o-check-circle',
            'mild' => 'heroicon-o-information-circle',
            'moderate' => 'heroicon-o-exclamation-triangle',
            'severe' => 'heroicon-o-shield-exclamation',
            'extremely_severe' => 'heroicon-o-bell-alert',
            default => 'heroicon-o-minus-circle',
        };
    }

    private static function durationText(Recommendation $record): string
    {
        return $record->duration_label
            ?: ($record->duration_minutes ? $record->duration_minutes.' menit' : 'Tidak diatur');
    }

    private static function priorityLabel(?string $priority): string
    {
        return match ($priority) {
            'personalized' => 'Personalisasi',
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => $priority ?: 'Tidak diatur',
        };
    }

    private static function priorityColor(?string $priority): string
    {
        return match ($priority) {
            'personalized' => 'info',
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'gray',
            default => 'gray',
        };
    }

    private static function formatMultiline(?string $text): string
    {
        return blank($text) ? '' : nl2br(e($text));
    }
}
