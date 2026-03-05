<?php

namespace App\Filament\Resources\Quotations\Schemas;

use App\Models\Client;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->schema([
                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('COT-0001'),

                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'borrador'  => 'Borrador',
                                'enviada'   => 'Enviada',
                                'aprobada'  => 'Aprobada',
                                'rechazada' => 'Rechazada',
                                'vencida'   => 'Vencida',
                            ])
                            ->required()
                            ->native(false)
                            ->default('borrador'),

                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Responsable')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        DatePicker::make('issue_date')
                            ->label('Fecha de Emisión')
                            ->required()
                            ->default(now()),

                        DatePicker::make('expiration_date')
                            ->label('Fecha de Vencimiento')
                            ->required()
                            ->after('issue_date'),
                    ])
                    ->columns(2),

                Section::make('Montos')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal  = floatval($get('subtotal') ?? 0);
                                $taxes     = floatval($get('taxes') ?? 0);
                                $discount  = floatval($get('discount') ?? 0);
                                $set('total', round($subtotal + $taxes - $discount, 2));
                            }),

                        TextInput::make('taxes')
                            ->label('Impuestos')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal  = floatval($get('subtotal') ?? 0);
                                $taxes     = floatval($get('taxes') ?? 0);
                                $discount  = floatval($get('discount') ?? 0);
                                $set('total', round($subtotal + $taxes - $discount, 2));
                            }),

                        TextInput::make('discount')
                            ->label('Descuento')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal  = floatval($get('subtotal') ?? 0);
                                $taxes     = floatval($get('taxes') ?? 0);
                                $discount  = floatval($get('discount') ?? 0);
                                $set('total', round($subtotal + $taxes - $discount, 2));
                            }),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->readOnly()
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Términos y Condiciones')
                    ->schema([
                        Textarea::make('terms_conditions')
                            ->label('Términos y Condiciones')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Metadata')
                    ->schema([
                        KeyValue::make('metadata')
                            ->label('Campos Adicionales')
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
