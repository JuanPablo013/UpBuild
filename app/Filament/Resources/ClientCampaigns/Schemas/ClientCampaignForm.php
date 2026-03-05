<?php

namespace App\Filament\Resources\ClientCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ClientCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_id')
                ->relationship('campaign', 'name')
                ->required(),

            Select::make('client_id')
                ->relationship('client', 'name')
                ->required(),

            Select::make('status')
                ->options([
                    'pendiente' => 'Pendiente',
                    'enviado' => 'Enviado',
                    'entregado' => 'Entregado',
                    'leido' => 'Leído',
                    'fallido' => 'Fallido',
                ])
                ->required(),

            DateTimePicker::make('sent_at'),

            DateTimePicker::make('delivered_at'),

            DateTimePicker::make('read_at'),

            KeyValue::make('metadata'),
            ]);
    }
}
