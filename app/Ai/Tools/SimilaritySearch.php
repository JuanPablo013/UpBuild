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

    // Request usa InteractsWithData, se accede así:
    $value = $request instanceof \Laravel\Ai\Tools\Request
    ? $request['value']   // ArrayAccess directo
    : (string) $request;

    Log::info('🔍 Valor extraído', ['value' => $value]);

    if (empty($value) || !is_string($value)) {
        return 'Error: El valor de entrada no puede estar vacío.';
    }

    // Generar embedding de la pregunta igual que tu EmbeddingService
    $embeddingResponse = \Laravel\Ai\Embeddings::for([$value])->generate(
        \Laravel\Ai\Enums\Lab::OpenAI,
        'text-embedding-3-small'
    );

    $embedding = $embeddingResponse->embeddings[0];

    $chunks = KnowledgeChunk::query()
        ->select('content')
        ->orderByRaw('embedding <-> ?::vector', [json_encode($embedding)])
        ->limit(5)
        ->get();

    if ($chunks->isEmpty()) {
        return 'No se encontró información relevante en la base de conocimiento.';
    }

    return $chunks->pluck('content')->implode("\n\n---\n\n");
}
    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema->string()->required()->description('La pregunta o texto a buscar en la base de conocimiento'),
        ];
    }
}