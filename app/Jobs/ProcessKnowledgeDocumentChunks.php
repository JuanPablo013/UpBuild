<?php

namespace App\Jobs;

use App\Models\KnowledgeDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ChunksDocuments\KnowledgeChunkService;

class ProcessKnowledgeDocumentChunks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public KnowledgeDocument $document) {}

    public function handle(KnowledgeChunkService $service): void
    {
        $service->processDocument($this->document);
    }
}
