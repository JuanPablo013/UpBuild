<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        Select::make('client_type')
                            ->label('Tipo de Cliente')
                            ->options([
                                'persona_natural' => 'Persona Natural',
                                'empresa' => 'Empresa',
                                'pyme' => 'PYME',
                                'corporativo' => 'Corporativo',
                            ])
                            ->required()
                            ->native(false)
                            ->default('persona_natural'),

                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('telephone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Ubicación')
                    ->schema([
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->label('Ciudad')
                            ->maxLength(255),

                        TextInput::make('department')
                            ->label('Departamento')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Información Comercial')
                    ->schema([
                        TextInput::make('estimated_budget')
                            ->label('Presupuesto Estimado')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->maxValue(999999999.99),

                        Select::make('funnel_stage')
                            ->label('Etapa del Embudo')
                            ->options([
                                'prospecto' => 'Prospecto',
                                'contactado' => 'Contactado',
                                'calificado' => 'Calificado',
                                'propuesta' => 'Propuesta',
                                'negociacion' => 'Negociación',
                                'cerrado_ganado' => 'Cerrado Ganado',
                                'cerrado_perdido' => 'Cerrado Perdido',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('ia_score')
                            ->label('Score IA')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('/100'),

                        Select::make('priority')
                            ->label('Prioridad')
                            ->options([
                                'baja' => 'Baja',
                                'media' => 'Media',
                                'alta' => 'Alta',
                                'urgente' => 'Urgente',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Datos Adicionales')
                    ->schema([
                        KeyValue::make('additional_data')
                            ->label('Información Adicional')
                            ->keyLabel('Campo')
                            ->valueLabel('Valor')
                            ->addActionLabel('Agregar campo')
                            ->reorderable(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
