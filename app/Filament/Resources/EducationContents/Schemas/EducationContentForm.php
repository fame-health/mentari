<?php

namespace App\Filament\Resources\EducationContents\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class EducationContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 3,
                ])
                    ->extraAttributes(['class' => 'mentari-content-form-grid'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                Section::make('Informasi utama')
                                    ->description('Tentukan identitas konten agar mudah ditemukan dan dipahami pengguna.')
                                    ->icon('heroicon-o-sparkles')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-editor-section--primary'])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 2,
                                    ])
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul konten')
                                            ->placeholder('Contoh: 5 Cara Sederhana Mengelola Stres')
                                            ->helperText('Gunakan judul yang singkat, spesifik, dan mudah dipahami.')
                                            ->prefixIcon('heroicon-o-document-text')
                                            ->required()
                                            ->maxLength(180)
                                            ->columnSpanFull(),
                                        Select::make('education_category_id')
                                            ->label('Kategori')
                                            ->relationship('category', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->placeholder('Pilih kategori konten')
                                            ->helperText('Kategori membantu pengguna menemukan topik yang relevan.')
                                            ->prefixIcon('heroicon-o-folder')
                                            ->required(),
                                        ToggleButtons::make('type')
                                            ->label('Format konten')
                                            ->options([
                                                'article' => 'Artikel',
                                                'infographic' => 'Infografis',
                                                'video' => 'Video',
                                            ])
                                            ->icons([
                                                'article' => 'heroicon-o-document-text',
                                                'infographic' => 'heroicon-o-photo',
                                                'video' => 'heroicon-o-play-circle',
                                            ])
                                            ->colors([
                                                'article' => 'info',
                                                'infographic' => 'warning',
                                                'video' => 'danger',
                                            ])
                                            ->grouped()
                                            ->inline()
                                            ->default('article')
                                            ->required(),
                                    ]),
                                Section::make('Isi konten')
                                    ->description('Tulis ringkasan terlebih dahulu, kemudian susun materi utama secara terstruktur.')
                                    ->icon('heroicon-o-book-open')
                                    ->extraAttributes(['class' => 'mentari-editor-section'])
                                    ->columns([
                                        'default' => 1,
                                        'lg' => 3,
                                    ])
                                    ->schema([
                                        Textarea::make('summary')
                                            ->label('Ringkasan singkat')
                                            ->placeholder('Jelaskan manfaat utama konten ini dalam 1–2 kalimat.')
                                            ->helperText('Ringkasan ditampilkan sebagai pengantar sebelum pengguna membuka konten.')
                                            ->rows(4)
                                            ->autosize()
                                            ->required()
                                            ->maxLength(500)
                                            ->columnSpan([
                                                'default' => 1,
                                                'lg' => 1,
                                            ]),
                                        RichEditor::make('body')
                                            ->label('Materi utama')
                                            ->helperText('Gunakan subjudul, daftar, dan paragraf pendek agar materi nyaman dibaca.')
                                            ->extraFieldWrapperAttributes([
                                                'class' => 'mentari-main-content-editor',
                                            ])
                                            ->toolbarButtons([
                                                ['bold', 'italic', 'underline', 'strike'],
                                                ['h2', 'h3'],
                                                ['bulletList', 'orderedList', 'blockquote'],
                                                ['link', 'undo', 'redo'],
                                            ])
                                            ->default(null)
                                            ->columnSpan([
                                                'default' => 1,
                                                'lg' => 2,
                                            ]),
                                    ]),
                                Section::make('Media & tampilan')
                                    ->description('Tambahkan media pendukung dan warna aksen untuk memperkuat tampilan konten.')
                                    ->icon('heroicon-o-photo')
                                    ->extraAttributes(['class' => 'mentari-editor-section'])
                                    ->columns([
                                        'default' => 1,
                                        'md' => 3,
                                    ])
                                    ->schema([
                                        TextInput::make('media_url')
                                            ->label('Tautan media')
                                            ->placeholder('https://contoh.com/media')
                                            ->helperText('Gunakan URL publik untuk gambar, infografis, atau video pendukung.')
                                            ->prefixIcon('heroicon-o-link')
                                            ->url()
                                            ->maxLength(255)
                                            ->default(null)
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 2,
                                            ]),
                                        ColorPicker::make('accent_color')
                                            ->label('Warna aksen')
                                            ->helperText('Opsional. Kosongkan untuk memakai warna bawaan MENTARI.')
                                            ->default(null),
                                    ]),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Pengaturan publikasi')
                                    ->description('Atur kapan dan bagaimana konten tersedia untuk pengguna.')
                                    ->icon('heroicon-o-paper-airplane')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-editor-section--publish'])
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Konten aktif')
                                            ->helperText('Matikan jika konten belum siap terlihat oleh pengguna.')
                                            ->inline(false)
                                            ->default(true)
                                            ->required(),
                                        DateTimePicker::make('published_at')
                                            ->label('Waktu publikasi')
                                            ->helperText('Waktu sekarang akan menerbitkan konten segera. Pilih waktu mendatang untuk menjadwalkan.')
                                            ->prefixIcon('heroicon-o-calendar-days')
                                            ->native(false)
                                            ->seconds(false)
                                            ->displayFormat('d M Y, H:i')
                                            ->default(fn () => now()),
                                    ]),
                                Section::make('Estimasi durasi')
                                    ->description('Bantu pengguna memperkirakan waktu yang dibutuhkan.')
                                    ->icon('heroicon-o-clock')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-editor-section--compact'])
                                    ->schema([
                                        TextInput::make('read_time_minutes')
                                            ->label('Durasi baca')
                                            ->placeholder('5')
                                            ->helperText('Isi angka perkiraan durasi dalam menit.')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(999)
                                            ->suffix('menit')
                                            ->default(null),
                                        TextInput::make('read_time_label')
                                            ->label('Label durasi khusus')
                                            ->placeholder('Contoh: Video 8 menit')
                                            ->helperText('Opsional. Jika diisi, label ini menggantikan durasi angka.')
                                            ->maxLength(50)
                                            ->default(null),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<Step>
     */
    public static function wizardSteps(): array
    {
        return [
            Step::make('Informasi Dasar')
                ->description('Judul, kategori, dan format')
                ->icon('heroicon-o-document-text')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Tentang konten')
                        ->description('Mulai dengan tiga informasi wajib. Semua pengaturan lain dapat dilengkapi setelahnya.')
                        ->icon('heroicon-o-sparkles')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-wizard-panel'])
                        ->columns([
                            'default' => 1,
                            'md' => 2,
                        ])
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul konten')
                                ->placeholder('Contoh: 5 Cara Sederhana Mengelola Stres')
                                ->helperText('Buat judul yang singkat dan langsung menjelaskan manfaat konten.')
                                ->prefixIcon('heroicon-o-document-text')
                                ->required()
                                ->maxLength(180)
                                ->columnSpanFull(),
                            Select::make('education_category_id')
                                ->label('Kategori')
                                ->relationship('category', 'title')
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->placeholder('Pilih kategori')
                                ->helperText('Digunakan untuk mengelompokkan konten di aplikasi.')
                                ->prefixIcon('heroicon-o-folder')
                                ->required(),
                            ToggleButtons::make('type')
                                ->label('Format konten')
                                ->options([
                                    'article' => 'Artikel',
                                    'infographic' => 'Infografis',
                                    'video' => 'Video',
                                ])
                                ->icons([
                                    'article' => 'heroicon-o-document-text',
                                    'infographic' => 'heroicon-o-photo',
                                    'video' => 'heroicon-o-play-circle',
                                ])
                                ->colors([
                                    'article' => 'info',
                                    'infographic' => 'warning',
                                    'video' => 'danger',
                                ])
                                ->grouped()
                                ->inline()
                                ->default('article')
                                ->required(),
                        ]),
                ]),
            Step::make('Tulis Konten')
                ->description('Ringkasan dan materi utama')
                ->icon('heroicon-o-pencil-square')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Isi yang akan dibaca pengguna')
                        ->description('Ringkas manfaat konten di sebelah kiri, lalu tulis materi lengkap di area yang lebih besar.')
                        ->icon('heroicon-o-book-open')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-wizard-panel'])
                        ->columns([
                            'default' => 1,
                            'lg' => 3,
                        ])
                        ->schema([
                            Textarea::make('summary')
                                ->label('Ringkasan singkat')
                                ->placeholder('Jelaskan manfaat utama konten dalam 1–2 kalimat.')
                                ->helperText('Ringkasan tampil sebelum pengguna membuka konten.')
                                ->rows(5)
                                ->required()
                                ->maxLength(500)
                                ->columnSpan([
                                    'default' => 1,
                                    'lg' => 1,
                                ]),
                            RichEditor::make('body')
                                ->label('Materi utama')
                                ->helperText('Gunakan subjudul, daftar, dan paragraf pendek agar mudah dibaca.')
                                ->extraFieldWrapperAttributes([
                                    'class' => 'mentari-main-content-editor',
                                ])
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['h2', 'h3'],
                                    ['bulletList', 'orderedList', 'blockquote'],
                                    ['link', 'undo', 'redo'],
                                ])
                                ->default(null)
                                ->columnSpan([
                                    'default' => 1,
                                    'lg' => 2,
                                ]),
                        ]),
                    Section::make('Pelengkap konten (opsional)')
                        ->description('Tambahkan media, warna, atau estimasi durasi jika diperlukan.')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->secondary()
                        ->collapsible()
                        ->collapsed()
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-wizard-optional'])
                        ->columns([
                            'default' => 1,
                            'md' => 2,
                        ])
                        ->schema([
                            TextInput::make('media_url')
                                ->label('Tautan media')
                                ->placeholder('https://contoh.com/media')
                                ->helperText('URL publik untuk gambar, infografis, atau video.')
                                ->prefixIcon('heroicon-o-link')
                                ->url()
                                ->maxLength(255)
                                ->default(null),
                            ColorPicker::make('accent_color')
                                ->label('Warna aksen')
                                ->helperText('Kosongkan untuk memakai warna bawaan MENTARI.')
                                ->default(null),
                            TextInput::make('read_time_minutes')
                                ->label('Estimasi durasi')
                                ->placeholder('5')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(999)
                                ->suffix('menit')
                                ->default(null),
                            TextInput::make('read_time_label')
                                ->label('Label durasi khusus')
                                ->placeholder('Contoh: Video 8 menit')
                                ->helperText('Jika diisi, label ini menggantikan estimasi angka.')
                                ->maxLength(50)
                                ->default(null),
                        ]),
                ]),
            Step::make('Publikasi')
                ->description('Atur waktu penayangan')
                ->icon('heroicon-o-paper-airplane')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Kapan konten ditayangkan?')
                        ->description('Periksa status dan waktu tayang. Setelah itu, simpan konten menggunakan tombol di bawah.')
                        ->icon('heroicon-o-calendar-days')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-wizard-panel mentari-wizard-publish'])
                        ->columns([
                            'default' => 1,
                            'md' => 2,
                        ])
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Tampilkan kepada pengguna')
                                ->helperText('Matikan jika konten masih berupa draf dan belum boleh terlihat.')
                                ->inline(false)
                                ->default(true)
                                ->required(),
                            DateTimePicker::make('published_at')
                                ->label('Waktu tayang')
                                ->helperText('Biarkan waktu sekarang untuk tayang langsung, atau pilih waktu mendatang.')
                                ->prefixIcon('heroicon-o-clock')
                                ->native(false)
                                ->seconds(false)
                                ->displayFormat('d M Y, H:i')
                                ->default(fn () => now()),
                        ]),
                ]),
        ];
    }
}
