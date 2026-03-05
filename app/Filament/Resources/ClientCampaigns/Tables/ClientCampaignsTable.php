<?php

namespace App\Filament\Resources\ClientCampaigns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.name')
                    ->label('Campaña')
                    ->searchable(),

                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pendiente',
                        'warning' => 'enviado',
                        'success' => 'entregado',
                        'info' => 'leido',
                        'danger' => 'fallido',
                    ]),

                TextColumn::make('sent_at')
                    ->dateTime(),

                TextColumn::make('delivered_at')
                    ->dateTime(),

                TextColumn::make('read_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                        'leido' => 'Leído',
                        'fallido' => 'Fallido',
                    ])
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
