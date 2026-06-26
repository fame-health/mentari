<?php

namespace App\Filament\Resources\Recommendations\Schemas;

use App\Models\Recommendation;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class RecommendationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 3,
                ])
                    ->extraAttributes(['class' => 'mentari-content-form-grid mentari-recommendation-form-grid'])
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                Section::make('Informasi utama')
                                    ->description('Tentukan jenis rekomendasi dan judul yang mudah dikenali admin maupun siswa.')
                                    ->icon('heroicon-o-light-bulb')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section mentari-recommendation-section--primary'])
                                    ->schema(self::identityFields()),
                                Section::make('Isi yang tampil ke siswa')
                                    ->description('Tulis dengan bahasa yang jelas, menenangkan, dan langsung memberi arahan.')
                                    ->icon('heroicon-o-chat-bubble-left-right')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section'])
                                    ->schema(self::contentFields()),
                            ]),
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Pengaturan aplikasi')
                                    ->description('Atur status, prioritas, warna, dan label durasi.')
                                    ->icon('heroicon-o-adjustments-horizontal')
                                    ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section mentari-recommendation-section--settings'])
                                    ->schema(self::settingFields()),
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
            Step::make('Jenis')
                ->description('Pilih kebutuhan rekomendasi')
                ->icon('heroicon-o-squares-2x2')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Rekomendasi ini untuk apa?')
                        ->description('Untuk skrip otomatis setelah DASS-21, pilih Skrip konseling singkat. Pilihan lain dipakai sebagai rekomendasi aktivitas umum.')
                        ->icon('heroicon-o-light-bulb')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section mentari-recommendation-section--primary'])
                        ->schema(self::identityFields()),
                ]),
            Step::make('Isi')
                ->description('Tulis pesan untuk siswa')
                ->icon('heroicon-o-pencil-square')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Kalimat rekomendasi')
                        ->description('Gunakan kalimat pendek, empatik, dan tidak membuat siswa merasa disalahkan.')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section'])
                        ->schema(self::contentFields()),
                ]),
            Step::make('Pengaturan')
                ->description('Status dan tampilan')
                ->icon('heroicon-o-adjustments-horizontal')
                ->completedIcon('heroicon-o-check-circle')
                ->schema([
                    Section::make('Siap ditampilkan?')
                        ->description('Lengkapi detail kecil agar rekomendasi mudah diprioritaskan dan tampil rapi di aplikasi.')
                        ->icon('heroicon-o-check-badge')
                        ->extraAttributes(['class' => 'mentari-editor-section mentari-recommendation-section mentari-recommendation-section--settings'])
                        ->schema(self::settingFields()),
                ]),
        ];
    }

    private static function identityFields(): array
    {
        return [
            ToggleButtons::make('category')
                ->label('Jenis rekomendasi')
                ->options(Recommendation::CATEGORY_LABELS)
                ->icons([
                    Recommendation::COUNSELING_SCRIPT_CATEGORY => 'heroicon-o-chat-bubble-left-right',
                    Recommendation::DASHBOARD_ANALYSIS_CATEGORY => 'heroicon-o-chart-bar-square',
                    'relaxation' => 'heroicon-o-sparkles',
                    'reflection' => 'heroicon-o-pencil-square',
                    'activity' => 'heroicon-o-bolt',
                ])
                ->colors([
                    Recommendation::COUNSELING_SCRIPT_CATEGORY => 'info',
                    Recommendation::DASHBOARD_ANALYSIS_CATEGORY => 'primary',
                    'relaxation' => 'success',
                    'reflection' => 'warning',
                    'activity' => 'gray',
                ])
                ->grouped()
                ->inline()
                ->live()
                ->default(Recommendation::COUNSELING_SCRIPT_CATEGORY)
                ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                    if (! in_array($state, [Recommendation::COUNSELING_SCRIPT_CATEGORY, Recommendation::DASHBOARD_ANALYSIS_CATEGORY], true)) {
                        $set('severity', null);

                        return;
                    }

                    if (blank($get('priority'))) {
                        $set('priority', 'personalized');
                    }

                    if (blank($get('duration_label'))) {
                        $set('duration_label', $state === Recommendation::DASHBOARD_ANALYSIS_CATEGORY
                            ? 'Analisis dashboard'
                            : 'Skrip singkat');
                    }
                })
                ->required()
                ->columnSpanFull(),
            TextInput::make('title')
                ->label('Judul')
                ->placeholder(fn (Get $get): string => $get('category') === Recommendation::COUNSELING_SCRIPT_CATEGORY
                    ? 'Contoh: Skrip konseling - Sedang'
                    : 'Contoh: Latihan napas 4-4')
                ->helperText('Judul dipakai admin untuk mengenali isi rekomendasi dengan cepat.')
                ->prefixIcon('heroicon-o-document-text')
                ->maxLength(150)
                ->required(),
        ];
    }

    private static function contentFields(): array
    {
        return [
            ToggleButtons::make('severity')
                ->label('Status DASS-21')
                ->options(Recommendation::SEVERITY_LABELS)
                ->icons([
                    'normal' => 'heroicon-o-check-circle',
                    'mild' => 'heroicon-o-information-circle',
                    'moderate' => 'heroicon-o-exclamation-triangle',
                    'severe' => 'heroicon-o-shield-exclamation',
                    'extremely_severe' => 'heroicon-o-bell-alert',
                ])
                ->colors([
                    'normal' => 'success',
                    'mild' => 'info',
                    'moderate' => 'warning',
                    'severe' => 'danger',
                    'extremely_severe' => 'danger',
                ])
                ->grouped()
                ->inline()
                ->visible(fn (Get $get): bool => in_array($get('category'), [Recommendation::COUNSELING_SCRIPT_CATEGORY, Recommendation::DASHBOARD_ANALYSIS_CATEGORY], true))
                ->helperText('Sistem memakai status ini untuk memilih konten otomatis setelah siswa menyelesaikan DASS-21.')
                ->required(fn (Get $get): bool => in_array($get('category'), [Recommendation::COUNSELING_SCRIPT_CATEGORY, Recommendation::DASHBOARD_ANALYSIS_CATEGORY], true))
                ->columnSpanFull(),
            TagsInput::make('main_points')
                ->label('Isi utama analisis')
                ->placeholder('Tulis satu poin lalu tekan Enter')
                ->helperText('Isi poin-poin utama yang tampil di bagian Analisis Data dashboard.')
                ->visible(fn (Get $get): bool => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY)
                ->required(fn (Get $get): bool => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY)
                ->columnSpanFull(),
            Textarea::make('education_message')
                ->label('Pesan edukasi singkat')
                ->placeholder('Contoh: Gejala masih ringan, tetapi perlu dipantau agar tidak bertambah berat.')
                ->helperText('Kalimat pendek yang tampil sebagai pesan edukasi di dashboard.')
                ->rows(3)
                ->autosize()
                ->visible(fn (Get $get): bool => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY)
                ->required(fn (Get $get): bool => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY)
                ->columnSpanFull(),
            Textarea::make('description')
                ->label(fn (Get $get): string => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY
                    ? 'Ringkasan internal'
                    : 'Deskripsi / skrip')
                ->placeholder(fn (Get $get): string => $get('category') === Recommendation::COUNSELING_SCRIPT_CATEGORY
                    ? 'Contoh: Hasil skrining menunjukkan gejala sedang. Saya menyarankan Anda berkonsultasi...'
                    : ($get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY
                        ? 'Contoh: Ringkasan isi utama analisis dashboard.'
                        : 'Contoh: Tarik napas selama 4 detik, tahan sebentar, lalu hembuskan perlahan.'))
                ->helperText(fn (Get $get): string => $get('category') === Recommendation::DASHBOARD_ANALYSIS_CATEGORY
                    ? 'Dipakai sebagai ringkasan cadangan bila poin utama belum tersedia.'
                    : 'Teks ini akan dibaca siswa. Hindari istilah teknis yang sulit dipahami.')
                ->rows(8)
                ->autosize()
                ->required()
                ->columnSpanFull(),
        ];
    }

    private static function settingFields(): array
    {
        return [
            Toggle::make('is_active')
                ->label('Aktif')
                ->helperText('Rekomendasi aktif dapat dikirim ke aplikasi siswa.')
                ->inline(false)
                ->default(true)
                ->required(),
            Select::make('priority')
                ->label('Prioritas')
                ->options([
                    'personalized' => 'Personalisasi',
                    'high' => 'Tinggi',
                    'medium' => 'Sedang',
                    'low' => 'Rendah',
                ])
                ->native(false)
                ->placeholder('Pilih prioritas')
                ->helperText('Personalisasi dipakai untuk skrip hasil DASS-21.')
                ->prefixIcon('heroicon-o-flag')
                ->default('personalized'),
            TextInput::make('duration_minutes')
                ->label('Durasi')
                ->placeholder('Contoh: 3')
                ->helperText('Opsional. Isi jika rekomendasi berupa aktivitas berdurasi.')
                ->numeric()
                ->minValue(1)
                ->suffix('menit')
                ->default(null),
            TextInput::make('duration_label')
                ->label('Label durasi')
                ->placeholder('Contoh: Skrip singkat')
                ->helperText('Opsional. Label ini tampil lebih manusiawi daripada angka saja.')
                ->prefixIcon('heroicon-o-clock')
                ->maxLength(50)
                ->default('Skrip singkat'),
            ColorPicker::make('accent_color')
                ->label('Warna aksen')
                ->helperText('Opsional. Gunakan warna untuk membedakan jenis rekomendasi.')
                ->default(null),
        ];
    }
}
