<?php

namespace App\Filament\Resources\CommunityPosts\Pages;

use App\Filament\Resources\CommunityPosts\CommunityPostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCommunityPosts extends ListRecords
{
    protected static string $resource = CommunityPostResource::class;

    protected string $view = 'filament.resources.community-posts.pages.list-community-posts';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah postingan')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah postingan komunitas')
                ->modalDescription('Tulis postingan komunitas langsung dari halaman ini.')
                ->modalSubmitActionLabel('Simpan postingan')
                ->modalWidth(Width::ThreeExtraLarge)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->createAnother(false),
        ];
    }
}
