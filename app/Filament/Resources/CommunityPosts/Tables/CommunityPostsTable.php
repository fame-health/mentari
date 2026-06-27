<?php

namespace App\Filament\Resources\CommunityPosts\Tables;

use App\Models\CommunityPost;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommunityPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['school', 'user']))
            ->columns([
                ViewColumn::make('content')
                    ->label('Percakapan Komunitas')
                    ->view('filament.resources.community-posts.tables.community-post-chat')
                    ->searchable(
                        query: fn (Builder $query, string $search): Builder => $query
                            ->where('content', 'like', "%{$search}%")
                            ->orWhere('tag', 'like', "%{$search}%")
                            ->orWhereHas('user', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('school', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%")),
                    )
                    ->sortable(
                        query: fn (Builder $query, string $direction): Builder => $query->orderBy('created_at', $direction),
                    ),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at'))
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading(fn (CommunityPost $record): string => 'Postingan '.$record->user?->name)
                    ->modalDescription('Detail postingan komunitas dan jumlah suka.')
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading(fn (CommunityPost $record): string => 'Edit postingan '.$record->user?->name)
                    ->modalDescription('Ubah isi postingan tanpa meninggalkan daftar chat komunitas.')
                    ->modalSubmitActionLabel('Simpan perubahan')
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
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
