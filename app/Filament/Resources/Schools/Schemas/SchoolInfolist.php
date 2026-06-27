<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.schools.infolists.school-overview')
                    ->columnSpanFull(),
            ]);
    }
}
