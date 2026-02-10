<?php
namespace App\Observers;

use App\Jobs\ProcessKnowledgeDocument;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\Log;

class KnowledgeDocumentObserver
{
    public function created(KnowledgeDocument $document): void
    {
        Log::info('🔥 Observer created triggered', [
            'id' => $document->id,
            'file_path' => $document->file_path,
            'file_type' => $document->file_type,
        ]);

        // Ejecutar sincrónicamente para debug
        ProcessKnowledgeDocument::dispatchSync($document);
    }

    public function updated(KnowledgeDocument $document): void
    {
        // Si el archivo cambió, reprocesar
        if ($document->isDirty('file_path')) {
            Log::info('🔥 Observer updated triggered - file changed');
            ProcessKnowledgeDocument::dispatchSync($document);
        }
    }
}