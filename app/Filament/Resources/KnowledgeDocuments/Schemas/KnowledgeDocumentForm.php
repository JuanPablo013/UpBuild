<?php
namespace App\Filament\Resources\KnowledgeDocuments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KnowledgeDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título del documento')
                ->required()
                ->maxLength(255),

            FileUpload::make('file_path')
                ->label('Archivo')
                ->disk('public')
                ->directory('knowledge')
                ->acceptedFileTypes(['application/pdf', 'text/plain'])
                ->maxSize(10240)
                ->required()
                ->columnSpanFull(),

            // ❌ ELIMINA el campo Hidden de file_type
            // Se manejará en mutateFormDataBeforeCreate

            Textarea::make('raw_text')
                ->label('Texto extraído')
                ->rows(12)
                ->columnSpanFull()
                ->disabled()
                ->dehydrated(false)
                ->visible(fn($record) => filled($record?->raw_text)),
        ]);
    }
}