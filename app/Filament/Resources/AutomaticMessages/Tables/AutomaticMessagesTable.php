<?php

namespace App\Filament\Resources\AutomaticMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AutomaticMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('campaign.name')
                    ->label('Campaña')
                    ->searchable(),

                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                BadgeColumn::make('channel')
                    ->colors([
                        'primary',
                    ]),

                BadgeColumn::make('state')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('recipients_count')
                    ->label('Destinatarios'),

                TextColumn::make('sent_count')
                    ->label('Enviados'),

                TextColumn::make('failed_count')
                    ->label('Fallidos'),

                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ]),

                SelectFilter::make('state')
                    ->options([
                        'draft' => 'Borrador',
                        'scheduled' => 'Programado',
                        'sent' => 'Enviado',
                        'failed' => 'Fallido',
                    ]),
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
