<?php

namespace App\Filament\Resources\ScreeningAnswers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScreeningAnswerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('screening_result_id')
                    ->numeric(),
                TextEntry::make('screening_question_id')
                    ->numeric(),
                TextEntry::make('score')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
