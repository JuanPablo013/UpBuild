<?php

namespace App\Filament\Resources\KnowledgeDocuments\Pages;

use App\Filament\Resources\KnowledgeDocuments\KnowledgeDocumentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Jobs\ProcessKnowledgeDocument;
use Illuminate\Support\Facades\Log;

class CreateKnowledgeDocument extends CreateRecord
{
    protected static string $resource = KnowledgeDocumentResource::class;

   protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_path'])) {
            // Extraer solo el nombre del archivo sin la carpeta
            $fullPath = $data['file_path']; // "knowledge/archivo.pdf"
            $filename = basename($fullPath); // "archivo.pdf"
            
            $data['file_path'] = $filename;
            $data['file_type'] = pathinfo($filename, PATHINFO_EXTENSION);
            
            Log::info('📝 Saving file', [
                'original' => $fullPath,
                'saved_as' => $filename,
                'type' => $data['file_type']
            ]);
        }

        return $data;
    }

}
