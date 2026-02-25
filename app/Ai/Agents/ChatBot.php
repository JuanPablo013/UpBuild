<?php

namespace App\Ai\Agents;

use App\Ai\Tools\SimilaritySearch;
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
    return <<<PROMPT
        Eres un asistente que responde preguntas usando una base de conocimiento.

        Cuando el usuario haga una pregunta:
        1. Debes usar la herramienta SimilaritySearch para buscar información relevante.
        2. Usa EXCLUSIVAMENTE la información devuelta por la herramienta para responder.
        3. Si la herramienta no devuelve información relevante, responde:
        "No tengo información suficiente para responder esa pregunta."

        Responde siempre en español, de forma clara y profesional.
        PROMPT;


    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [

        ];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
             new SimilaritySearch(),
        ];
    }
}
