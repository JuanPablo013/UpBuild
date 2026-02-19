<?php

namespace App\Jobs;

use App\Models\KnowledgeDocument;
use App\Services\ChunksDocuments\KnowledgeChunkService;
use App\Services\Embeddings\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessKnowledgeDocumentChunks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public KnowledgeDocument $document
    ) {}

    public function handle(
        KnowledgeChunkService $chunkService,
        EmbeddingService $embeddingService
    ): void {
        // 1️⃣ Crear los chunks
        $chunkService->processDocument($this->document);

        // 2️⃣ Generar embeddings para esos chunks
        $embeddingService->embedDocumentChunks($this->document->id);
    }
}
