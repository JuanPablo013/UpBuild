<?php
namespace App\Ai\Tools;

use App\Models\KnowledgeChunk;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Embeddings;
use Stringable;
use Illuminate\Support\Facades\Log;

class SimilaritySearch implements Tool
{
    // 0.5 = muy estricto | 0.8 = moderado | 1.3 = permisivo (recomendado para PDFs con OCR ruidoso)
    private const SIMILARITY_THRESHOLD = 1.3;

    public function name(): string
    {
        return 'search_knowledge_base';
    }

    public function description(): Stringable|string
    {
        return 'Busca información relevante en la base de conocimiento. Llama esta herramienta con la pregunta del usuario para encontrar documentos relacionados.';
    }

    public function handle(mixed ...$arguments): Stringable|string
    {
        $request = $arguments[0];

        $value = $request instanceof \Laravel\Ai\Tools\Request
            ? $request['value']
            : (string) $request;

        Log::info('🔍 [SimilaritySearch] Query recibida', ['value' => $value]);

        if (empty($value) || !is_string($value)) {
            Log::warning('⚠️ [SimilaritySearch] Valor vacío o inválido');
            return 'Error: El valor de entrada no puede estar vacío.';
        }

        $embeddingResponse = \Laravel\Ai\Embeddings::for([$value])->generate(
            \Laravel\Ai\Enums\Lab::OpenAI,
            'text-embedding-3-small'
        );
        $embedding = $embeddingResponse->embeddings[0];

        Log::info('✅ [SimilaritySearch] Embedding generado correctamente');

        $chunksWithDistance = KnowledgeChunk::query()
            ->selectRaw('content, (embedding <-> ?::vector) as distance', [json_encode($embedding)])
            ->orderBy('distance')
            ->limit(5)
            ->get();

        Log::info('📊 [SimilaritySearch] Top 5 resultados con distancias', [
            'query'   => $value,
            'umbral'  => self::SIMILARITY_THRESHOLD,
            'results' => $chunksWithDistance->map(fn($c) => [
                'distance'    => round($c->distance, 4),
                'pasa_umbral' => $c->distance < self::SIMILARITY_THRESHOLD ? '✅' : '❌',
                'preview'     => substr($c->content, 0, 100),
            ])->toArray(),
        ]);

        $chunks = $chunksWithDistance->filter(
            fn($c) => $c->distance < self::SIMILARITY_THRESHOLD
        );

        if ($chunks->isEmpty()) {
            Log::warning('🚫 [SimilaritySearch] Ningún chunk pasó el umbral', [
                'umbral'           => self::SIMILARITY_THRESHOLD,
                'mejor_distancia'  => round($chunksWithDistance->first()?->distance ?? 999, 4),
            ]);
            return 'FUERA_DE_DOMINIO';
        }

        Log::info('🎯 [SimilaritySearch] Chunks encontrados dentro del umbral', [
            'cantidad' => $chunks->count(),
        ]);

        return $chunks->pluck('content')->implode("\n\n---\n\n");
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema->string()->required()->description('La pregunta o texto a buscar en la base de conocimiento'),
        ];
    }
}