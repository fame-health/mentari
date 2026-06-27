<?php

namespace App\Filament\Resources\Schools\Tables;

use App\Models\School;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Sekolah')
                    ->description(fn ($record): string => $record->code ?: 'Kode dibuat otomatis')
                    ->icon('heroicon-o-building-office-2')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(45)
                    ->placeholder('Belum diisi')
                    ->searchable(),
                TextColumn::make('students_count')
                    ->label('Siswa')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('classrooms_count')
                    ->label('Kelas')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('screening_results_count')
                    ->label('Screening')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('active_risk_alerts_count')
                    ->label('Alert aktif')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('manageClassrooms')
                    ->label('Kelola kelas')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->button()
                    ->fillForm(fn (School $record): array => [
                        'classrooms' => $record->classrooms()
                            ->get()
                            ->map(fn ($classroom): array => [
                                'id' => $classroom->id,
                                'name' => $classroom->name,
                                'sort_order' => $classroom->sort_order,
                                'is_active' => $classroom->is_active,
                            ])
                            ->all(),
                    ])
                    ->schema([
                        Repeater::make('classrooms')
                            ->label('Daftar kelas')
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('name')
                                    ->label('Nama kelas')
                                    ->required()
                                    ->maxLength(50)
                                    ->distinct()
                                    ->placeholder('Contoh: X IPA 1'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah kelas')
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Kelas baru')
                            ->collapsible()
                            ->reorderable(false),
                    ])
                    ->modalHeading(fn (School $record): string => 'Kelola Kelas '.$record->name)
                    ->modalDescription('Tambahkan atau ubah kelas yang tersedia untuk siswa di sekolah ini.')
                    ->modalSubmitActionLabel('Simpan daftar kelas')
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->action(function (School $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $classrooms = collect($data['classrooms'] ?? []);
                            $submittedClassroomIds = $classrooms
                                ->pluck('id')
                                ->filter()
                                ->map(fn (mixed $id): int => (int) $id);
                            $classroomIdsToDelete = $record->classrooms()
                                ->when(
                                    $submittedClassroomIds->isNotEmpty(),
                                    fn ($query) => $query->whereNotIn('id', $submittedClassroomIds),
                                )
                                ->pluck('id');

                            if ($classroomIdsToDelete->isNotEmpty()) {
                                DB::table('users')
                                    ->whereIn('classroom_id', $classroomIdsToDelete)
                                    ->update([
                                        'classroom_id' => null,
                                        'level' => null,
                                    ]);

                                $record->classrooms()
                                    ->whereIn('id', $classroomIdsToDelete)
                                    ->delete();
                            }

                            $classrooms
                                ->each(function (array $classroomData) use ($record): void {
                                    $attributes = [
                                        'name' => trim($classroomData['name']),
                                        'sort_order' => (int) $classroomData['sort_order'],
                                        'is_active' => (bool) $classroomData['is_active'],
                                    ];

                                    if (filled($classroomData['id'] ?? null)) {
                                        $classroom = $record->classrooms()->findOrFail($classroomData['id']);
                                        $classroom->update($attributes);

                                        return;
                                    }

                                    $record->classrooms()->create($attributes);
                                });
                        });

                        Notification::make()
                            ->title('Daftar kelas berhasil disimpan')
                            ->body($record->name.' sekarang memiliki '.$record->classrooms()->count().' kelas.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record): string => 'Ringkasan '.$record->name)
                    ->modalDescription('Profil sekolah, kelas, dan ringkasan data terbaru.')
                    ->modalIcon('heroicon-o-building-office-2')
                    ->modalIconColor('info')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->stickyModalHeader(),
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading(fn ($record): string => 'Edit '.$record->name)
                    ->modalDescription('Kelola informasi sekolah dan daftar kelas dalam satu form.')
                    ->modalSubmitActionLabel('Simpan perubahan')
                    ->modalWidth(Width::ThreeExtraLarge),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
