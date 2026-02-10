<?php

namespace App\Filament\Resources\KnowledgeDocuments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KnowledgeDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 📌 TÍTULO (manual)
                TextInput::make('title')
                    ->label('Título del documento')
                    ->required()
                    ->maxLength(255),

                // 📎 ARCHIVO (manual)
                FileUpload::make('file_path')
                    ->label('Archivo')
                    ->disk('public')
                    ->directory('knowledge')
                    ->preserveFilenames()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'text/plain',
                        'application/msword', // .doc
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                        'application/octet-stream', // A veces .docx se detecta así
                    ])
                    ->required()
                    ->columnSpanFull(),

                // 🧠 TIPO DE ARCHIVO (automático)
                Hidden::make('file_type')
                    ->dehydrateStateUsing(
                        fn($state, $component) =>
                        pathinfo($component->getContainer()
                            ->getState()['file_path'] ?? '', PATHINFO_EXTENSION)
                    ),

                // 📖 TEXTO EXTRAÍDO (solo lectura)
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
