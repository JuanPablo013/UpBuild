<?php

namespace App\Filament\Resources\ClientCampaigns;

use App\Filament\Resources\ClientCampaigns\Pages\CreateClientCampaign;
use App\Filament\Resources\ClientCampaigns\Pages\EditClientCampaign;
use App\Filament\Resources\ClientCampaigns\Pages\ListClientCampaigns;
use App\Filament\Resources\ClientCampaigns\Schemas\ClientCampaignForm;
use App\Filament\Resources\ClientCampaigns\Tables\ClientCampaignsTable;
use App\Models\ClientCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClientCampaignResource extends Resource
{
    protected static ?string $model = ClientCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Cliente campaña';

    protected static string | UnitEnum | null $navigationGroup = 'Marketing';

      protected static ?int $navigationSort = 3;


    public static function form(Schema $schema): Schema
    {
        return ClientCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientCampaignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientCampaigns::route('/'),
            'create' => CreateClientCampaign::route('/create'),
            'edit' => EditClientCampaign::route('/{record}/edit'),
        ];
    }
}
