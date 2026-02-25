<?php

namespace App\Console\Commands;

use App\Ai\Agents\ChatBot;
use Illuminate\Console\Command;
use App\Models\User;
use Laravel\Ai\Enums\Lab;

class PruebaChatBot extends Command
{
    protected $signature = 'prueba:chat';
    protected $description = 'Prueba de RAG con base de datos de conocimientos';

    public function handle(): void
    {
        // 1. Validar usuario (necesario para persistir la conversación)
        $user = User::first();

        if (!$user) {
            $this->error('❌ No hay usuarios en la base de datos.');
            return;
        }

        $this->info('🚀 Iniciando Asistente RAG (Groq + Llama 3.3)');
        $this->line('Usando conocimiento previo almacenado en la DB.');
        $this->line('---');

        $agent = new ChatBot();
        $conversationId = null;

        while (true) {
            $question = $this->ask('¿Qué quieres preguntarle a la IA? (escribe "salir" para terminar)');

            if (empty($question) || in_array(strtolower(trim($question)), ['salir', 'exit', 'quit'])) {
                $this->info('👋 Saliendo...');
                break;
            }

            try {
                // 2. Si es la primera pregunta, creamos la conversación. 
                // Si ya existe, continuamos la anterior.
                $pendingPrompt = $conversationId 
                    ? $agent->continue($conversationId, as: $user) 
                    : $agent->forUser($user);

                $response = $pendingPrompt->prompt(
                    $question,
                    provider: Lab::OpenAI, // Usamos el driver de Ollama local
                    model: 'gpt-4.1-mini',       // Modelo local de Ollama para respuestas
                    timeout: 120
                );

                // Guardamos el ID para mantener el hilo de la charla
                $conversationId = $response->conversationId;

                $this->info("\n🤖 Respuesta de la IA:");
                $this->line((string) $response);
                $this->comment("\n(ID Conversación: {$conversationId})");
                $this->line('---');

            } catch (\Exception $e) {
                $this->error('❌ Error: ' . $e->getMessage());
            }
        }
    }
}