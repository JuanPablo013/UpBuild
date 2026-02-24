<?php

namespace App\Ai\Tools;

use App\Models\KnowledgeChunk;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Tools\Request;
use Stringable;

class SimilaritySearch implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Esta herramienta realiza una búsqueda de similitud en los chunks de conocimiento para encontrar los más relevantes según el texto de entrada.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request->input('value');

        if(empty($query)) {
            return 'Error: El valor de entrada no puede estar vacío.';
        }

        $embedding = Embeddings::create($query)->embedding;

         $chunks = KnowledgeChunk::query()
            ->select('content')
            ->orderByRaw('embedding <-> ?', [$embedding])
            ->limit(5)
            ->get();

         if ($chunks->isEmpty()) {
            return 'No se encontró información relevante en la base de conocimiento.';
        }

         return $chunks
            ->pluck('content')
            ->implode("\n\n---\n\n");
    }

    

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema->string()->required(),
        ];
    }
}
