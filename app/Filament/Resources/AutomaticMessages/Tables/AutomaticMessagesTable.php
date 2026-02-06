<?php

namespace App\Filament\Resources\AutomaticMessages\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\AutomaticMessage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Illuminate\Support\Facades\Log;

class AutomaticMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.name')
                    ->label('Campaña')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->subject),

                TextColumn::make('channel')
                    ->label('Canal')
                    ->badge()
                    ->colors([
                        'primary' => 'email',
                        'success' => 'sms',
                        'info' => 'whatsapp',
                        'warning' => 'push_notification',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        match ($state) {
                            'email' => 'Email',
                            'sms' => 'SMS',
                            'whatsapp' => 'WhatsApp',
                            'push_notification' => 'Push',
                            default => $state,
                        }
                    ),

                TextColumn::make('state')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'gray' => 'borrador',
                        'info' => 'programado',
                        'warning' => 'enviando',
                        'success' => 'enviado',
                        'danger' => 'fallido',
                        'secondary' => 'cancelado',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst($state)
                    ),

                TextColumn::make('recipients_count')
                    ->label('Destinatarios')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('sent_count')
                    ->label('Enviados')
                    ->numeric()
                    ->sortable()
                    ->color('success'),

                TextColumn::make('failed_count')
                    ->label('Fallidos')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),

                TextColumn::make('success_rate')
                    ->label('Tasa de Éxito')
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'danger' => fn($state) => $state < 70,
                        'warning' => fn($state) => $state >= 70 && $state < 90,
                        'success' => fn($state) => $state >= 90,
                    ]),

                TextColumn::make('scheduled_at')
                    ->label('Programado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label('Enviado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('send_now')
                    ->label('Enviar Ahora')
                    ->icon(Heroicon::PaperAirplane)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enviar Campaña')
                    ->modalDescription(
                        fn($record) =>
                        "¿Enviar el mensaje a {$record->campaign->clients()->count()} clientes?"
                    )
                    ->visible(fn($record) => $record->canBeSent())
                    ->action(function (AutomaticMessage $record) {
                        try {
                            Log::info('Iniciando envío de campaña desde Filament', [
                                'message_id' => $record->id,
                                'campaign_id' => $record->campaign_id,
                                'clients_count' => $record->campaign->clients()->count(),
                            ]);

                            $record->send();

                            Log::info('Job despachado correctamente', [
                                'message_id' => $record->id,
                            ]);

                            Notification::make()
                                ->title('Campaña en proceso')
                                ->success()
                                ->body('Los mensajes están siendo enviados en segundo plano. Verifica que el queue worker esté corriendo.')
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Error al enviar campaña desde Filament', [
                                'message_id' => $record->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);

                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                Action::make('schedule_send')
                    ->label('Programar Envío')
                    ->icon(Heroicon::OutlinedClock)
                    ->color('warning')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Fecha y Hora de Envío')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->action(function (AutomaticMessage $record, array $data) {
                        try {
                            $record->schedule(new \DateTime($data['scheduled_at']));

                            Notification::make()
                                ->title('Envío Programado')
                                ->success()
                                ->body('El mensaje será enviado en la fecha programada.')
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

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
