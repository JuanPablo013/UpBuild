<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('client_type')
                    ->label('Tipo')
                    ->badge()
                    ->colors([
                        'primary' => 'persona_natural',
                        'success' => 'empresa',
                        'warning' => 'pyme',
                        'info' => 'corporativo',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'persona_natural' => 'Persona Natural',
                            'empresa' => 'Empresa',
                            'pyme' => 'PYME',
                            'corporativo' => 'Corporativo',
                            default => $state,
                        }
                    ),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon(Heroicon::Envelope),

                TextColumn::make('telephone')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon(Heroicon::Phone),

                TextColumn::make('funnel_stage')
                    ->label('Etapa')
                    ->badge()
                    ->colors([
                        'gray' => 'prospecto',
                        'info' => 'contactado',
                        'primary' => 'calificado',
                        'warning' => 'propuesta',
                        'secondary' => 'negociacion',
                        'success' => 'cerrado_ganado',
                        'danger' => 'cerrado_perdido',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst(str_replace('_', ' ', $state))
                    ),

                TextColumn::make('ia_score')
                    ->label('Score IA')
                    ->badge()
                    ->colors([
                        'danger' => fn($state) => $state < 60,
                        'warning' => fn($state) => $state >= 60 && $state < 80,
                        'success' => fn($state) => $state >= 80,
                    ])
                    ->suffix('/100')
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->colors([
                        'gray' => 'baja',
                        'info' => 'media',
                        'warning' => 'alta',
                        'danger' => 'urgente',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst($state)
                    ),

                TextColumn::make('campaigns_count')
                    ->label('Campañas')
                    ->counts('campaigns')
                    ->badge()
                    ->color('success'),

                /* TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), */
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
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
