<?php

namespace App\Filament\Resources\KnowledgeDocuments;

use App\Filament\Resources\KnowledgeDocuments\Pages\CreateKnowledgeDocument;
use App\Filament\Resources\KnowledgeDocuments\Pages\EditKnowledgeDocument;
use App\Filament\Resources\KnowledgeDocuments\Pages\ListKnowledgeDocuments;
use App\Filament\Resources\KnowledgeDocuments\Schemas\KnowledgeDocumentForm;
use App\Filament\Resources\KnowledgeDocuments\Tables\KnowledgeDocumentsTable;
use App\Models\KnowledgeDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KnowledgeDocumentResource extends Resource
{
    protected static ?string $model = KnowledgeDocument::class;

protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-arrow-up';


    protected static ?string $recordTitleAttribute = 'Procesar Docuemntos';

    protected static ?string $pluralModelLabel = 'Documentos de Conocimiento';

    protected static ?int $navigationSort = 2;

    protected static string | UnitEnum | null $navigationGroup = 'Inteligencia Artificial';




    public static function form(Schema $schema): Schema
    {
        return KnowledgeDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeDocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeDocuments::route('/'),
            'create' => CreateKnowledgeDocument::route('/create'),
            'edit' => EditKnowledgeDocument::route('/{record}/edit'),
        ];
    }
}
