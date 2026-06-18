<?php

namespace App\Filament\Resources\EducationContents\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EducationContentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('education_category_id')
                    ->numeric(),
                TextEntry::make('title'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('read_time_minutes')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('read_time_label')
                    ->placeholder('-'),
                TextEntry::make('summary')
                    ->columnSpanFull(),
                TextEntry::make('body')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('media_url')
                    ->placeholder('-'),
                TextEntry::make('accent_color')
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
