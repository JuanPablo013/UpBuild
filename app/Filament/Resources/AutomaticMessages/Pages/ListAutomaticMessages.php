<?php

namespace App\Filament\Resources\AutomaticMessages\Pages;

use App\Filament\Resources\AutomaticMessages\AutomaticMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutomaticMessages extends ListRecords
{
    protected static string $resource = AutomaticMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
