<?php

namespace App\Filament\Resources\KnowledgeDocuments\Pages;

use App\Filament\Resources\KnowledgeDocuments\KnowledgeDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Jobs\ProcessKnowledgeDocument;
use Illuminate\Support\Facades\Log;

class EditKnowledgeDocument extends EditRecord
{
    protected static string $resource = KnowledgeDocumentResource::class;

      protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar file_type si cambió el archivo
        if (isset($data['file_path'])) {
            $data['file_type'] = pathinfo($data['file_path'], PATHINFO_EXTENSION);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('file_path')) {
            Log::info('🔄 File changed, reprocessing');
            ProcessKnowledgeDocument::dispatchSync($this->record);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
