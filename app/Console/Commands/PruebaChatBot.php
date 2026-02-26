<?php

namespace App\Console\Commands;

use App\Ai\Agents\ChatBot;
use App\Models\User;
use Illuminate\Console\Command;
use Laravel\Ai\Enums\Lab;

class PruebaChatBot extends Command
{
    protected $signature = 'prueba:chat {--user= : ID del usuario (opcional, usa el primero por defecto)}';
    protected $description = 'Prueba de RAG con base de datos de conocimientos';

    public function handle(): void
    {
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : User::first();

        if (!$user) {
            $this->error('❌ No hay usuarios en la base de datos.');
            return;
        }

        $this->info('🚀 Iniciando Asistente RAG');
        $this->line("👤 Usuario: {$user->name} (ID: {$user->id})");
        $this->line('📚 Usando conocimiento almacenado en la DB.');
        $this->line(str_repeat('-', 50));

        $agent = new ChatBot();
        $conversationId = null;

        while (true) {
            $question = $this->ask("\n💬 Tu pregunta");

            if (empty($question) || in_array(strtolower(trim($question)), ['salir', 'exit', 'quit', 'q'])) {
                $this->info('👋 Saliendo...');
                break;
            }

            try {
                $this->line('⏳ Buscando en base de conocimiento...');

                $pendingPrompt = $conversationId
                    ? $agent->continue($conversationId, as: $user)
                    : $agent->forUser($user);

                $response = $pendingPrompt->prompt(
                    $question,
                    provider: Lab::OpenAI,
                    model: 'gpt-4.1-mini',
                    timeout: 120
                );

                $conversationId = $response->conversationId;

                $this->newLine();
                $this->info('🤖 Respuesta:');
                $this->line((string) $response);
                $this->newLine();
                $this->comment("💾 Conversación ID: {$conversationId}");
                $this->line(str_repeat('-', 50));

            } catch (\Throwable $e) {
                $this->error('❌ Error: ' . $e->getMessage());
                
                if ($this->option('verbose')) {
                    $this->line($e->getTraceAsString());
                }

                // Preguntar si quiere continuar o salir tras el error
                if (!$this->confirm('¿Deseas intentar con otra pregunta?', true)) {
                    break;
                }
            }
        }

        $this->info('✅ Sesión terminada.');
        if ($conversationId) {
            $this->comment("📝 Puedes retomar esta conversación con el ID: {$conversationId}");
        }
    }
}