<?php

namespace App\Filament\Resources\ClientCampaigns\Pages;

use App\Filament\Resources\ClientCampaigns\ClientCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientCampaign extends EditRecord
{
    protected static string $resource = ClientCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
