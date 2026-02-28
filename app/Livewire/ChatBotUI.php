<?php
namespace App\Livewire;

use Livewire\Component;
use App\Ai\Agents\ChatBot;
use App\Models\User;
use Laravel\Ai\Enums\Lab;

class ChatBotUI extends Component
{
    public string $message = '';
    public array $messages = [];
    public ?string $conversationId = null;
    public bool $loading = false;

    public function mount(): void
    {
        $this->messages = [
            [
                'role' => 'bot',
                'text' => '¡Hola! Soy Alex, tu asesor inmobiliario. ¿Qué tipo de propiedad estás buscando?',
            ]
        ];
    }

    public function send()
    {
        if (trim($this->message) === '') {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'text' => $this->message,
        ];

        $question = $this->message;
        $this->message = '';
        $this->loading = true;

        $user = auth()->user() ?? User::first();
        $agent = new ChatBot();

        try {
            $pendingPrompt = $this->conversationId
                ? $agent->continue($this->conversationId, as: $user)
                : $agent->forUser($user);

            $response = $pendingPrompt->prompt(
                $question,
                provider: Lab::OpenAI,
                model: 'gpt-4.1-mini',
                timeout: 120
            );

            $this->conversationId = $response->conversationId;

            $this->messages[] = [
                'role' => 'bot',
                'text' => (string) $response,
            ];

        } catch (\Throwable $e) {
            $this->messages[] = [
                'role' => 'bot',
                'text' => '❌ Error: ' . $e->getMessage(),
            ];
        }

        $this->loading = false;
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.chat-bot-u-i')
            ->layout('layouts.app');
    }
}