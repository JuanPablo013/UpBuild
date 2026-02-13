<?php

namespace App\Services\ChunksDocuments;

use App\Models\KnowledgeDocument;
use App\Models\KnowledgeChunk;
use Illuminate\Support\Str;

class KnowledgeChunkService
{
    public function processDocument(KnowledgeDocument $document, int $chunkSize = 1000, int $overlap = 200): void
    {
        $text = trim($document->raw_text);

        if (empty($text)) {
            return;
        }

        $chunks = $this->splitText($text, $chunkSize, $overlap);

        foreach ($chunks as $index => $chunk) {
            KnowledgeChunk::create([
                'knowledge_document_id' => $document->id,
                'chunk_index'           => $index,
                'content'               => $chunk,
            ]);
        }
    }

    protected function splitText(string $text, int $size, int $overlap): array
    {
        $chunks = [];
        $length = Str::length($text);
        $start  = 0;

        while ($start < $length) {
            $chunk = Str::substr($text, $start, $size);
            $chunks[] = trim($chunk);

            $start += ($size - $overlap);
        }

        return $chunks;
    }
}
