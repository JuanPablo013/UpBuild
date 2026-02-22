<?php

namespace App\Ai\Agents;

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class ChatBot implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'Eres un asistente virtual de ayuda para usuarios.

                Tu función es responder preguntas utilizando únicamente la información
                que se te proporciona como contexto en la conversación.

                Reglas importantes:
                - Responde siempre en español.
                - Usa un tono claro, amable y profesional.
                - Si la información no está en el contexto, responde:
                "No tengo información suficiente para responder esa pregunta."
                - No inventes datos ni asumas información que no esté explícitamente indicada.
                - Si la respuesta es un proceso, explícalo paso a paso.
                - Si la pregunta es ambigua, solicita una aclaración breve.

                Tu objetivo es ayudar al usuario de la forma más clara y precisa posible.';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
