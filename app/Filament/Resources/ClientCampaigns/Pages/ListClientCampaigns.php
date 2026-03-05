<?php

namespace App\Filament\Resources\ClientCampaigns\Pages;

use App\Filament\Resources\ClientCampaigns\ClientCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientCampaigns extends ListRecords
{
    protected static string $resource = ClientCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
