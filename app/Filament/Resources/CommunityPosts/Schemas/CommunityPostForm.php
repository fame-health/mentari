<?php

namespace App\Filament\Resources\CommunityPosts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommunityPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->extraAttributes(['class' => 'mentari-community-form-grid'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ])
                            ->schema([
                                Section::make('Isi percakapan')
                                    ->description('Teks utama yang tampil sebagai bubble komunitas.')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->extraAttributes(['class' => 'mentari-community-modal-section mentari-community-modal-section--message'])
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Pesan komunitas')
                                            ->placeholder('Tulis postingan komunitas di sini...')
                                            ->required()
                                            ->rows(9)
                                            ->autosize()
                                            ->maxLength(3000)
                                            ->extraAttributes(['class' => 'mentari-community-message-field']),
                                    ]),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Pengirim & konteks')
                                    ->description('Identitas postingan untuk admin.')
                                    ->icon('heroicon-o-user-circle')
                                    ->extraAttributes(['class' => 'mentari-community-modal-section mentari-community-modal-section--meta'])
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Pengirim')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('school_id')
                                            ->label('Sekolah')
                                            ->relationship('school', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Tidak terkait sekolah')
                                            ->default(null),
                                        TextInput::make('tag')
                                            ->label('Tag')
                                            ->prefix('#')
                                            ->placeholder('curhat')
                                            ->maxLength(80)
                                            ->default(null),
                                    ]),
                                Section::make('Status postingan')
                                    ->description('Atur visibilitas dan angka interaksi.')
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->extraAttributes(['class' => 'mentari-community-modal-section mentari-community-modal-section--status'])
                                    ->schema([
                                        Toggle::make('is_pinned')
                                            ->label('Sematkan postingan')
                                            ->helperText('Postingan disematkan tampil lebih menonjol di daftar.')
                                            ->default(false)
                                            ->required(),
                                        TextInput::make('likes_count')
                                            ->label('Jumlah suka')
                                            ->prefixIcon('heroicon-o-hand-thumb-up')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
