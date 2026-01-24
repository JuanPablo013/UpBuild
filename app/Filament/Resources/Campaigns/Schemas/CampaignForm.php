<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la Campaña')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipo de Campaña')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                                'redes_sociales' => 'Redes Sociales',
                                'mixta' => 'Mixta',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'borrador' => 'Borrador',
                                'programada' => 'Programada',
                                'activa' => 'Activa',
                                'pausada' => 'Pausada',
                                'completada' => 'Completada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->required()
                            ->native(false)
                            ->default('borrador'),
                    ])
                    ->columns(2),

                Section::make('Fechas')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Fecha de Inicio')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),

                        DatePicker::make('final_date')
                            ->label('Fecha de Finalización')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('start_date')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Segmentación')
                    ->schema([
                        KeyValue::make('target_segment')
                            ->label('Segmento Objetivo')
                            ->keyLabel('Criterio')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar criterio')
                            ->reorderable()
                            ->helperText('Define los criterios de segmentación para esta campaña'),
                    ])
                    ->collapsible(),

                Section::make('Configuración de IA')
                    ->schema([
                        KeyValue::make('ia_configuration')
                            ->label('Parámetros de IA')
                            ->keyLabel('Parámetro')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar parámetro')
                            ->reorderable()
                            ->helperText('Configuración específica para el motor de IA'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
