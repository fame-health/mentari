<?php

namespace App\Filament\Resources\CommunityPosts\Schemas;

use App\Models\CommunityPost;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class CommunityPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi percakapan')
                    ->description('Preview pesan yang tampil di komunitas.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->extraAttributes(['class' => 'mentari-community-view-section mentari-community-view-section--message'])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->formatStateUsing(fn (?string $state): string => self::formatMultiline($state))
                            ->html()
                            ->prose()
                            ->size(TextSize::Medium)
                            ->weight(FontWeight::Medium)
                            ->color('gray')
                            ->placeholder('Isi postingan belum tersedia.'),
                    ]),
                Section::make('Detail postingan')
                    ->description('Ringkasan konteks dan interaksi postingan.')
                    ->icon('heroicon-o-information-circle')
                    ->extraAttributes(['class' => 'mentari-community-view-section mentari-community-view-section--meta'])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Pengirim')
                            ->icon('heroicon-o-user-circle')
                            ->weight(FontWeight::SemiBold)
                            ->placeholder('Pengguna tidak ditemukan'),
                        TextEntry::make('school.name')
                            ->label('Sekolah')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('Tidak terkait sekolah'),
                        TextEntry::make('tag')
                            ->label('Tag')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => filled($state) ? '#'.$state : '-')
                            ->color('info')
                            ->icon('heroicon-o-hashtag')
                            ->placeholder('Tanpa tag'),
                        TextEntry::make('post_status')
                            ->label('Status')
                            ->state(fn (CommunityPost $record): string => $record->is_pinned ? 'Disematkan' : 'Normal')
                            ->badge()
                            ->color(fn (CommunityPost $record): string => $record->is_pinned ? 'warning' : 'gray')
                            ->icon(fn (CommunityPost $record): string => $record->is_pinned ? 'heroicon-o-bookmark' : 'heroicon-o-chat-bubble-left-right'),
                        TextEntry::make('likes_count')
                            ->label('Jumlah suka')
                            ->numeric()
                            ->icon('heroicon-o-hand-thumb-up')
                            ->color('primary'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path')
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d M Y, H:i')
                            ->badge()
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->visible(fn (CommunityPost $record): bool => $record->trashed()),
                    ]),
            ]);
    }

    private static function formatMultiline(?string $text): string
    {
        if (blank($text)) {
            return '';
        }

        return nl2br(e($text));
    }
}
