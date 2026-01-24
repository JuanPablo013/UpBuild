<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    public function chat(string $message, array $context = []): string
    {
        $messages = [];
        
        // Agregar contexto si existe
        if (!empty($context)) {
            $messages[] = [
                'role' => 'system',
                'content' => 'Eres un asistente útil.'
            ];
        }
        
        // Agregar mensaje del usuario
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model' => 'llama-3.3-70b-versatile', // Modelo gratuito
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);

        if ($response->failed()) {
            throw new \Exception('Error al conectar con la IA: ' . $response->body());
        }

        return $response->json('choices.0.message.content');
    }

    public function generateText(string $prompt): string
    {
        return $this->chat($prompt);
    }
}