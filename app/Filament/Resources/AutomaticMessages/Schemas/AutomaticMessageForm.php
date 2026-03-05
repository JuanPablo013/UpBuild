<?php

namespace App\Filament\Resources\AutomaticMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AutomaticMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_id')
                    ->relationship('campaign', 'name')
                    ->required()
                    ->searchable(),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),

                Textarea::make('content')
                    ->rows(6)
                    ->required(),

                Select::make('channel')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->required(),

                Select::make('state')
                    ->options([
                        'draft' => 'Borrador',
                        'scheduled' => 'Programado',
                        'sent' => 'Enviado',
                        'failed' => 'Fallido',
                    ])
                    ->required(),

                DateTimePicker::make('scheduled_at'),

                DateTimePicker::make('sent_at'),

                TextInput::make('recipients_count')
                    ->numeric(),

                TextInput::make('sent_count')
                    ->numeric(),

                TextInput::make('failed_count')
                    ->numeric(),

                Textarea::make('ia_response')
                    ->label('Respuesta IA')
                    ->rows(4),
            ]);
    }
}
