<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->icon(Heroicon::OutlinedDocumentText),

                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Responsable')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray'    => 'borrador',
                        'info'    => 'enviada',
                        'success' => 'aprobada',
                        'danger'  => 'rechazada',
                        'warning' => 'vencida',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'borrador'  => 'Borrador',
                            'enviada'   => 'Enviada',
                            'aprobada'  => 'Aprobada',
                            'rechazada' => 'Rechazada',
                            'vencida'   => 'Vencida',
                            default     => ucfirst($state),
                        }
                    ),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('taxes')
                    ->label('Impuestos')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('discount')
                    ->label('Descuento')
                    ->money('COP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('issue_date')
                    ->label('Emisión')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('expiration_date')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->expiration_date < now() && $record->status !== 'aprobada' ? 'danger' : null),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'borrador'  => 'Borrador',
                        'enviada'   => 'Enviada',
                        'aprobada'  => 'Aprobada',
                        'rechazada' => 'Rechazada',
                        'vencida'   => 'Vencida',
                    ])
                    ->native(false),

                SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); //
    }
}
