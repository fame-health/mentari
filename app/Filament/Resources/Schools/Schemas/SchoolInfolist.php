<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\School;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas sekolah')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama sekolah')
                            ->weight('bold'),
                        TextEntry::make('code')
                            ->label('Kode otomatis')
                            ->badge()
                            ->color('primary')
                            ->copyable(),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('Alamat belum diisi')
                            ->columnSpanFull(),
                    ]),
                Section::make('Jawaban otomatis dari sistem')
                    ->description('Ringkasan ini dihitung langsung dari data pengguna dan screening sekolah.')
                    ->icon('heroicon-o-sparkles')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('students_count')
                            ->label('Berapa siswa terdaftar?')
                            ->state(fn (School $record): int => self::studentsCount($record))
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-users'),
                        TextEntry::make('counselors_count')
                            ->label('Berapa konselor terdaftar?')
                            ->state(fn (School $record): int => self::counselorsCount($record))
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-o-user-group'),
                        TextEntry::make('screening_results_count')
                            ->label('Berapa screening selesai?')
                            ->state(fn (School $record): int => self::screeningCount($record))
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-clipboard-document-check'),
                        TextEntry::make('active_risk_alerts_count')
                            ->label('Apakah ada alert aktif?')
                            ->state(function (School $record): string {
                                $count = self::activeAlertCount($record);

                                return $count > 0 ? $count.' alert perlu ditangani' : 'Tidak ada alert aktif';
                            })
                            ->badge()
                            ->color(fn (School $record): string => self::activeAlertCount($record) > 0 ? 'danger' : 'success')
                            ->icon(fn (School $record): string => self::activeAlertCount($record) > 0
                                ? 'heroicon-o-exclamation-triangle'
                                : 'heroicon-o-check-circle'),
                    ]),
                Section::make('Riwayat data')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d M Y, H:i')
                            ->visible(fn (School $record): bool => $record->trashed()),
                    ]),
            ]);
    }

    private static function studentsCount(School $record): int
    {
        return (int) ($record->getAttribute('students_count')
            ?? $record->users()->where('role', 'student')->count());
    }

    private static function counselorsCount(School $record): int
    {
        return (int) ($record->getAttribute('counselors_count')
            ?? $record->users()->where('role', 'counselor')->count());
    }

    private static function screeningCount(School $record): int
    {
        return (int) ($record->getAttribute('screening_results_count')
            ?? $record->screeningResults()->count());
    }

    private static function activeAlertCount(School $record): int
    {
        return (int) ($record->getAttribute('active_risk_alerts_count')
            ?? $record->riskAlerts()->whereNull('dismissed_at')->count());
    }
}
