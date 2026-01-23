<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la empresa')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nit')
                    ->label('NIT')
                    ->maxLength(255),

                TextInput::make('website')
                    ->label('Sitio web')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://ejemplo.com'),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(50),

                KeyValue::make('custom_fields')
                    ->label('Campos personalizados')
                    ->nullable()
                    ->keyLabel('Campo')
                    ->valueLabel('Valor')
                    ->addActionLabel('Agregar campo'),
            ]);
    }
}
