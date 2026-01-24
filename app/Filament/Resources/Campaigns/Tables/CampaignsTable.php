<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Models\Campaign;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(
                        fn(Campaign $record): string =>
                        $record->description ? substr($record->description, 0, 50) . '...' : ''
                    ),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->colors([
                        'primary' => 'email',
                        'success' => 'sms',
                        'info' => 'whatsapp',
                        'warning' => 'redes_sociales',
                        'gray' => 'mixta',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'redes_sociales' => 'Redes Sociales',
                            'mixta' => 'Mixta',
                            default => $state,
                        }
                    ),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray' => 'borrador',
                        'info' => 'programada',
                        'success' => 'activa',
                        'warning' => 'pausada',
                        'primary' => 'completada',
                        'danger' => 'cancelada',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst($state)
                    ),

                TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date()
                    ->sortable(),

                TextColumn::make('final_date')
                    ->label('Fin')
                    ->date()
                    ->sortable(),

                TextColumn::make('clientes_count')
                    ->label('Clientes')
                    ->counts('clients')
                    ->badge()
                    ->color('success'),

                TextColumn::make('automatic_messages_count')
                    ->label('Mensajes')
                    ->counts('automaticMessages')
                    ->badge()
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Campaign $record) => $record->canBeActivated())
                    ->action(fn(Campaign $record) => $record->activate())
                    ->successNotificationTitle('Campaña activada correctamente'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
