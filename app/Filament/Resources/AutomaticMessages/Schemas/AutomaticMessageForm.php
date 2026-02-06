<?php

namespace App\Filament\Resources\AutomaticMessages\Schemas;

use App\Models\Campaign;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class AutomaticMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuración del Mensaje')
                    ->schema([
                        Select::make('campaign_id')
                            ->label('Campaña')
                            ->relationship('campaign', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $campaign = Campaign::find($state);
                                    $set('channel', $campaign?->type);
                                }
                            })
                            ->columnSpanFull(),

                        Select::make('channel')
                            ->label('Canal')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                                'push_notification' => 'Push Notification',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Select::make('state')
                            ->label('Estado')
                            ->options([
                                'borrador' => 'Borrador',
                                'programado' => 'Programado',
                                'enviando' => 'Enviando',
                                'enviado' => 'Enviado',
                                'fallido' => 'Fallido',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->native(false)
                            ->default('Borrador'),
                    ])
                    ->columns(2),

                Section::make('Contenido')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Asunto')
                            ->maxLength(255)
                            ->required(fn(Get $get) => $get('channel') === 'email')
                            ->visible(fn(Get $get) => $get('channel') === 'email'),

                        RichEditor::make('content')
                            ->label('Contenido del Mensaje')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ]),
                    ]),

                Section::make('Programación')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Programar Envío')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                    ])
                    ->collapsible(),

                Section::make('Configuración de IA')
                    ->schema([
                        KeyValue::make('ia_response')
                            ->label('Respuesta de IA')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar campo')
                            ->disabled()
                            ->helperText('Este campo es llenado automáticamente por la IA'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn($record) => $record?->ia_response),
            ]);
    }
}
