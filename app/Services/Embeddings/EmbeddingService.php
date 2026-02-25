<?php

namespace App\Services\Embeddings;

use App\Models\KnowledgeChunk;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class EmbeddingService
{
    /**
     * Genera embeddings en batch para los chunks de un documento
     */
    public function embedDocumentChunks(
        int $documentId,
        int $batchSize = 10
    ): void {
        KnowledgeChunk::where('knowledge_document_id', $documentId)
            ->whereNull('embedding')
            ->orderBy('id')
            ->chunk($batchSize, function ($chunks) {

                // 1️⃣ Solo el texto (limpio)
                $contents = $chunks
                    ->pluck('content')
                    ->map(fn ($text) => trim($text))
                    ->filter()
                    ->values()
                    ->toArray();

                if (empty($contents)) {
                    return;
                }

                Log::info('🧠 Generating embeddings batch', [
                    'chunks' => count($contents),
                ]);

                // 2️⃣ Llamada a Ollama local con retry y backoff
                $response = retry(3, function () use ($contents) {
                    return Embeddings::for($contents)->generate(
                        Lab::OpenAI, // Driver Ollama local
                        'text-embedding-3-small'       // Modelo local de embeddings
                    );
                }, 2000);

                // 3️⃣ Guardamos cada vector
                foreach ($chunks as $index => $chunk) {
                    if (!isset($response->embeddings[$index])) {
                        Log::warning('⚠️ Embedding faltante', [
                            'chunk_id' => $chunk->id,
                        ]);
                        continue;
                    }

                    $chunk->update([
                        'embedding' => $response->embeddings[$index],
                    ]);
                }

                // 4️⃣ Micro pausa para no saturar el provider
                usleep(300_000); // 0.3 segundos
            });
    }
}
